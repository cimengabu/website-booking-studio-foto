<?php

namespace App\Exceptions;

use Exception;

class SlotUnavailableException extends Exception
{
    protected $message = 'Jadwal yang Anda pilih sudah tidak tersedia atau telah terisi oleh pelanggan lain.';
}
