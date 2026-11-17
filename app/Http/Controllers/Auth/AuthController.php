<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /** Maximum failed attempts before OTP verification is required. */
    private const MAX_ATTEMPTS_BEFORE_OTP = 3;

    public function showLogin(Request $request)
    {
        if ($request->filled('package_id')) {
            session(['booking_package_id' => $request->query('package_id')]);
        }
        if ($request->filled('room_id')) {
            session(['booking_room_id' => $request->query('room_id')]);
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        // --- Wrong password / user not found ---
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            if ($user) {
                $newCount = ($user->failed_login_count ?? 0) + 1;
                $user->failed_login_count = $newCount;
                $user->save();

                // After MAX_ATTEMPTS_BEFORE_OTP failures → generate OTP
                if ($newCount >= self::MAX_ATTEMPTS_BEFORE_OTP) {
                    $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $user->login_otp = $otp;
                    $user->login_otp_expires_at = now()->addMinutes(10);
                    $user->otp_verified_at = null;
                    $user->save();

                    // Store pending email in session so OTP page knows which user
                    $request->session()->put('otp_pending_email', $email);

                    ActivityLog::record(
                        $user->id,
                        'auth_otp_sent',
                        "Kode OTP dikirim ke nomor HP {$user->phone} setelah {$newCount}x gagal login."
                    );

                    // In a real app: send OTP via SMS gateway here
                    // SmsGateway::send($user->phone, "Kode OTP SnapSpace: {$otp}");

                    return redirect()->route('login.otp')
                        ->with('otp_sent', true)
                        ->with('masked_phone', $this->maskPhone($user->phone));
                }
            }

            ActivityLog::record(
                null,
                'auth_login_failed',
                "Percobaan login gagal untuk email: {$email}."
            );

            $remaining = self::MAX_ATTEMPTS_BEFORE_OTP - ($user?->failed_login_count ?? 0);

            return back()->withErrors([
                'email' => "Email atau password yang Anda masukkan salah. Tersisa {$remaining}x percobaan sebelum verifikasi HP diperlukan.",
            ])->onlyInput('email');
        }

        // --- Correct credentials: check if OTP verification is pending ---
        if (($user->failed_login_count ?? 0) >= self::MAX_ATTEMPTS_BEFORE_OTP
            && is_null($user->otp_verified_at)) {
            // Force OTP flow
            $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->login_otp = $otp;
            $user->login_otp_expires_at = now()->addMinutes(10);
            $user->otp_verified_at = null;
            $user->save();

            $request->session()->put('otp_pending_email', $email);

            ActivityLog::record(
                $user->id,
                'auth_otp_sent',
                "Kode OTP dikirim ke {$user->phone} sebelum login diizinkan."
            );

            return redirect()->route('login.otp')
                ->with('otp_sent', true)
                ->with('masked_phone', $this->maskPhone($user->phone));
        }

        // --- Login successful ---
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Reset failed attempts & clear OTP
        $user->failed_login_count = 0;
        $user->login_otp = null;
        $user->login_otp_expires_at = null;
        $user->otp_verified_at = null;
        $user->save();

        ActivityLog::record(
            $user->id,
            'auth_login_success',
            "User {$user->name} ({$user->email}) berhasil login dengan role {$user->role}."
        );

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isStaff()) {
            return redirect()->intended(route('staff.scan'));
        }

        $bookingParams = array_filter([
            'package_id' => session()->pull('booking_package_id'),
            'room_id' => session()->pull('booking_room_id'),
        ]);

        $defaultUrl = ! empty($bookingParams) ? route('booking.wizard', $bookingParams) : route('home');

        return redirect()->intended($defaultUrl);
    }

    /** Show the OTP verification page. */
    public function showOtp(Request $request)
    {
        if (! $request->session()->has('otp_pending_email')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /** Verify the OTP and log the user in. */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $email = $request->session()->get('otp_pending_email');
        if (! $email) {
            return redirect()->route('login')->withErrors(['otp' => 'Sesi OTP telah habis. Silakan login kembali.']);
        }

        $user = User::where('email', $email)->first();
        if (! $user || ! $user->login_otp) {
            return redirect()->route('login')->withErrors(['email' => 'Data sesi tidak valid.']);
        }

        // Expired?
        if (! $user->login_otp_expires_at || now()->isAfter($user->login_otp_expires_at)) {
            $user->login_otp = null;
            $user->login_otp_expires_at = null;
            $user->save();
            $request->session()->forget('otp_pending_email');

            return redirect()->route('login')->withErrors(['email' => 'Kode OTP sudah kedaluwarsa. Silakan coba login kembali.']);
        }

        // Wrong OTP?
        if ($request->input('otp') !== $user->login_otp) {
            ActivityLog::record(
                $user->id,
                'auth_otp_failed',
                "Kode OTP salah dimasukkan untuk email: {$email}."
            );

            return back()->withErrors(['otp' => 'Kode OTP salah. Periksa kembali SMS yang dikirim ke nomor HP Anda.']);
        }

        // OTP verified — log user in
        $user->failed_login_count = 0;
        $user->login_otp = null;
        $user->login_otp_expires_at = null;
        $user->otp_verified_at = now();
        $user->save();

        Auth::login($user, false);
        $request->session()->regenerate();
        $request->session()->forget('otp_pending_email');

        ActivityLog::record(
            $user->id,
            'auth_otp_success',
            "OTP berhasil diverifikasi. User {$user->name} berhasil login."
        );

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.scan');
        }

        $bookingParams = array_filter([
            'package_id' => session()->pull('booking_package_id'),
            'room_id' => session()->pull('booking_room_id'),
        ]);

        $defaultUrl = ! empty($bookingParams) ? route('booking.wizard', $bookingParams) : route('home');

        return redirect()->intended($defaultUrl)->with('success', 'Verifikasi OTP berhasil! Selamat datang kembali.');
    }

    /** Resend OTP. */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('otp_pending_email');
        if (! $email) {
            return redirect()->route('login');
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return redirect()->route('login');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->login_otp = $otp;
        $user->login_otp_expires_at = now()->addMinutes(10);
        $user->otp_verified_at = null;
        $user->save();

        ActivityLog::record(
            $user->id,
            'auth_otp_resent',
            "Kode OTP baru dikirim ulang ke {$user->phone}."
        );

        // In a real app: SmsGateway::send($user->phone, "Kode OTP SnapSpace (baru): {$otp}");

        return back()->with('otp_sent', true)->with('masked_phone', $this->maskPhone($user->phone));
    }

    public function showRegister(Request $request)
    {
        if ($request->filled('package_id')) {
            session(['booking_package_id' => $request->query('package_id')]);
        }
        if ($request->filled('room_id')) {
            session(['booking_room_id' => $request->query('room_id')]);
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'consent_pdp' => ['nullable'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'privacy_consent_at' => now(),
            'privacy_consent_ip' => $request->ip(),
        ]);

        ActivityLog::record(
            $user->id,
            'auth_register',
            "Pengguna {$user->name} ({$user->email}) mendaftar akun baru dan menyetujui pemrosesan data (UU PDP)."
        );

        Auth::login($user);

        $bookingParams = array_filter([
            'package_id' => session()->pull('booking_package_id'),
            'room_id' => session()->pull('booking_room_id'),
        ]);

        $defaultUrl = ! empty($bookingParams) ? route('booking.wizard', $bookingParams) : route('home');

        return redirect()->intended($defaultUrl)->with('success', 'Selamat datang di SnapSpace! Akun Anda berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Anda telah keluar dari akun pelanggan. Silakan login dengan akun lain.');
        }

        return redirect()->route('home')->with('success', 'Anda telah keluar dari sistem.');
    }

    /** Mask phone number: 0812****7890 */
    private function maskPhone(?string $phone): string
    {
        if (! $phone || strlen($phone) < 8) {
            return '****';
        }

        $visible = 4;
        $start = substr($phone, 0, $visible);
        $end = substr($phone, -4);
        $masked = str_repeat('*', max(0, strlen($phone) - $visible - 4));

        return $start.$masked.$end;
    }
}
