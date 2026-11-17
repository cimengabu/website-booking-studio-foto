<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Laporan Keuangan</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 11pt; color: #1a1a1a; }
  .title { font-size: 16pt; font-weight: bold; color: #1F3A2E; text-align: center; }
  .subtitle { font-size: 11pt; color: #555555; text-align: center; }
  table { border-collapse: collapse; width: 100%; margin-top: 15px; }
  th { background-color: #1F3A2E; color: #FFFFFF; font-weight: bold; border: 1px solid #142820; padding: 10px 8px; text-align: center; font-size: 11pt; }
  td { border: 1px solid #D1D5DB; padding: 7px 10px; font-size: 10pt; vertical-align: middle; }
  tr:nth-child(even) td { background-color: #F9FAFB; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .text-code { mso-number-format:"\@"; text-align: center; font-family: Consolas, monospace; }
  .currency { mso-number-format:"\#\,\#\#0"; text-align: right; font-weight: 600; }
  .total-row td { background-color: #FEF3C7 !important; font-weight: bold; font-size: 11pt; border-top: 2px solid #D97706; border-bottom: 2px solid #D97706; }
</style>
</head>
<body>
  <table>
    <tr>
      <td colspan="10" class="title">LAPORAN KEUANGAN SNAPSPACE STUDIO</td>
    </tr>
    <tr>
      <td colspan="10" class="subtitle">Periode: {{ \Carbon\Carbon::parse($from)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($to)->translatedFormat('d F Y') }} | Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</td>
    </tr>
    <tr><td colspan="10" style="border:none; height: 10px;"></td></tr>
    <thead>
      <tr>
        <th style="width: 40px;">No</th>
        <th style="width: 170px;">ID Transaksi</th>
        <th style="width: 150px;">Kode Booking</th>
        <th style="width: 180px;">Nama Pelanggan</th>
        <th style="width: 140px;">Ruangan / Studio</th>
        <th style="width: 140px;">Paket Foto</th>
        <th style="width: 110px;">Tipe Bayar</th>
        <th style="width: 100px;">Metode</th>
        <th style="width: 130px;">Jumlah (Rp)</th>
        <th style="width: 150px;">Tanggal Lunas</th>
      </tr>
    </thead>
    <tbody>
      @forelse($payments as $index => $p)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td class="text-code">{{ $p->order_id }}</td>
          <td class="text-code">{{ $p->booking->booking_code ?? '-' }}</td>
          <td>{{ $p->booking->user->name ?? 'Tamu Walk-in' }}</td>
          <td>{{ $p->booking->room->name ?? '-' }}</td>
          <td>{{ $p->booking->package->name ?? '-' }}</td>
          <td class="text-center">{{ strtoupper($p->payment_kind) }}</td>
          <td class="text-center">{{ strtoupper($p->payment_type ?? 'QRIS') }}</td>
          <td class="currency">{{ (int) $p->gross_amount }}</td>
          <td class="text-center">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center" style="padding: 20px; color: #888;">Tidak ada data transaksi pembayaran pada periode ini.</td>
        </tr>
      @endforelse
    </tbody>
    @if($payments->isNotEmpty())
    <tfoot>
      <tr class="total-row">
        <td colspan="8" class="text-right">TOTAL PENDAPATAN :</td>
        <td class="currency">{{ (int) $totalRevenue }}</td>
        <td></td>
      </tr>
    </tfoot>
    @endif
  </table>
</body>
</html>
