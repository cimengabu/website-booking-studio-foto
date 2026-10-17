<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('customer.dashboard.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Hak Akses & Portabilitas Data (UU PDP No. 27 Tahun 2022)
     * Mengunduh seluruh arsip data pribadi pelanggan dalam format JSON
     */
    public function exportPersonalData()
    {
        $user = Auth::user();

        $data = [
            'app' => 'SnapSpace Studio',
            'export_date' => now()->toIso8601String(),
            'pdp_compliance' => 'UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi',
            'user_profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'loyalty_points' => $user->loyalty_points,
                'privacy_consent_at' => $user->privacy_consent_at?->toIso8601String(),
                'created_at' => $user->created_at?->toIso8601String(),
            ],
            'bookings' => $user->bookings()->with(['room', 'package', 'bookingAddons.addon', 'payments'])->get(),
            'reviews' => $user->reviews()->get(),
            'favorites' => $user->favorites()->with('room')->get(),
        ];

        $filename = "SnapSpace-Data-Pribadi-{$user->id}-".date('Ymd').'.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Hak Penghapusan Data / Right to be Forgotten (UU PDP No. 27 Tahun 2022)
     * Menghapus identitas akun pengguna dan menganonimkan riwayat transaksi
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_delete' => 'required|in:HAPUS',
        ], [
            'confirm_delete.in' => 'Ketik "HAPUS" untuk mengonfirmasi penghapusan akun.',
        ]);

        $user = Auth::user();

        // 1. Catat Audit Log penghapusan akun atas permintaan pemilik data
        ActivityLog::record(
            $user->id,
            'pdp_account_erasure',
            "Akun {$user->name} ({$user->email}) dihapus atas permintaan pemilik data (UU PDP No. 27/2022)."
        );

        // 2. Hapus file avatar fisik jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect('/')->with('success', 'Akun dan seluruh data pribadi Anda telah berhasil dihapus dari sistem SnapSpace Studio.');
    }
}
