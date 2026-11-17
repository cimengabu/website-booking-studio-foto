<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$payments = App\Models\Payment::whereHas('booking', function($q) { $q->where('user_id', 7); })->get();
foreach ($payments as $p) {
    echo "PAYMENT: ID {$p->id} | Booking: {$p->booking->booking_code} | Kind: {$p->payment_kind} | Gross: {$p->gross_amount} | Status: {$p->transaction_status} | Created: {$p->created_at}\n";
}

$u = App\Models\User::find(7);
echo "CURRENT STEFANO POINTS: {$u->loyalty_points}\n";
