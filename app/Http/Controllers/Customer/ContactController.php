<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('customer.contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'subject' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'subject.required' => 'Topik pertanyaan wajib dipilih.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal 10 karakter.',
        ]);

        $recipientEmail = config('mail.admin_contact_email', env('ADMIN_CONTACT_EMAIL', 'ind4hwijaya06@gmail.com'));

        Log::info("Contact form submission forwarded to {$recipientEmail}", $validated);

        try {
            Mail::raw(
                "Ada pesan baru dari pengunjung website SnapSpace Studio:\n\n"
                ."• Nama Pengirim: {$validated['name']}\n"
                ."• Nomor WhatsApp: {$validated['phone']}\n"
                ."• Email Pengirim: ".($validated['email'] ?: 'Tidak dicantumkan')."\n"
                ."• Topik / Subjek: {$validated['subject']}\n\n"
                ."Isi Pesan:\n"
                ."----------------------------------------\n"
                ."{$validated['message']}\n"
                ."----------------------------------------\n\n"
                ."Waktu Pengiriman: ".now()->translatedFormat('l, d F Y H:i')." WIB\n"
                ."Website: SnapSpace Studio Klaten",
                function ($message) use ($recipientEmail, $validated) {
                    $message->to($recipientEmail)
                        ->subject("[Pesan Baru SnapSpace] {$validated['subject']} — {$validated['name']}");

                    if (! empty($validated['email'])) {
                        $message->replyTo($validated['email'], $validated['name']);
                    }
                }
            );
        } catch (\Throwable $e) {
            Log::warning("Gagal mengirim email notifikasi kontak ke {$recipientEmail}: ".$e->getMessage());
        }

        return redirect()->route('contact')->with('contact_success', true);
    }
}
