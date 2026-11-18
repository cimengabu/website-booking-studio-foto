<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesan Baru dari Website SnapSpace</title>
<style>
  body { margin: 0; padding: 0; background-color: #FAF7F2; font-family: 'Segoe UI', Arial, sans-serif; color: #1A1A1A; line-height: 1.6; }
  .wrapper { width: 100%; max-width: 600px; margin: 30px auto; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #E5E0D8; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
  .header { background-color: #1F3A2E; padding: 24px 32px; text-align: center; }
  .header h1 { margin: 0; font-size: 20px; color: #F2C94C; font-weight: bold; letter-spacing: 0.5px; }
  .header p { margin: 4px 0 0 0; color: #E5E0D8; font-size: 12px; }
  .content { padding: 32px; }
  .badge { display: inline-block; padding: 4px 12px; background-color: #FEF3C7; color: #92400E; font-size: 11px; font-weight: bold; border-radius: 20px; text-transform: uppercase; margin-bottom: 16px; }
  .intro { font-size: 15px; margin-bottom: 24px; color: #4A4A4A; }
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  .info-table td { padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #F0ECE4; }
  .info-table td.label { width: 35%; color: #8A8A8A; font-weight: 600; }
  .info-table td.value { width: 65%; color: #1A1A1A; font-weight: 600; }
  .message-box { background-color: #FAF7F2; border-left: 4px solid #C8A27A; padding: 16px 20px; border-radius: 8px; font-size: 14px; color: #2D2D2D; white-space: pre-wrap; margin-bottom: 28px; font-style: italic; }
  .btn-container { text-align: center; margin-bottom: 20px; }
  .btn-wa { display: inline-block; background-color: #25D366; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px; margin: 4px; }
  .btn-email { display: inline-block; background-color: #1F3A2E; color: #FFFFFF; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; font-size: 13px; margin: 4px; }
  .footer { background-color: #F8F6F3; padding: 20px 32px; text-align: center; font-size: 11px; color: #8A8A8A; border-top: 1px solid #E5E0D8; }
</style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>SnapSpace Studio Klaten</h1>
      <p>Notifikasi Pesan Masuk dari Formulir Kontak Website</p>
    </div>
    
    <div class="content">
      <span class="badge">Pesan Baru Pelanggan</span>
      <p class="intro">Halo Admin, Anda menerima pesan pertanyaan baru dari pengunjung website resmi SnapSpace Studio:</p>
      
      <table class="info-table">
        <tr>
          <td class="label">Nama Pengirim</td>
          <td class="value">{{ $validated['name'] }}</td>
        </tr>
        <tr>
          <td class="label">Nomor WhatsApp</td>
          <td class="value">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $validated['phone']) }}" style="color: #2E9E6B; text-decoration: none;">
              {{ $validated['phone'] }}
            </a>
          </td>
        </tr>
        <tr>
          <td class="label">Email Pengirim</td>
          <td class="value">{{ $validated['email'] ?: 'Tidak dicantumkan' }}</td>
        </tr>
        <tr>
          <td class="label">Subjek / Topik</td>
          <td class="value">{{ $validated['subject'] }}</td>
        </tr>
        <tr>
          <td class="label">Waktu Masuk</td>
          <td class="value">{{ now()->translatedFormat('l, d F Y - H:i') }} WIB</td>
        </tr>
      </table>

      <p style="font-weight: bold; font-size: 13px; margin-bottom: 8px; color: #1A1A1A;">Isi Pesan:</p>
      <div class="message-box">{{ $validated['message'] }}</div>

      <div class="btn-container">
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $validated['phone']) }}?text=Halo%20{{ urlencode($validated['name']) }}%2C%20terima%20kasih%20telah%20menghubungi%20SnapSpace%20Studio." class="btn-wa" target="_blank">
          Balas via WhatsApp
        </a>
        @if(!empty($validated['email']))
          <a href="mailto:{{ $validated['email'] }}?subject=Re:%20{{ urlencode($validated['subject']) }}%20-%20SnapSpace%20Studio" class="btn-email">
            Balas via Email
          </a>
        @endif
      </div>
    </div>

    <div class="footer">
      <p style="margin: 0;">Email ini dikirim otomatis oleh sistem SnapSpace Studio (https://snapspace.web.id).</p>
      <p style="margin: 4px 0 0 0;">Waru, RT 17 / RW 07, Pandanan, Wonosari, Klaten, Jawa Tengah.</p>
    </div>
  </div>
</body>
</html>
