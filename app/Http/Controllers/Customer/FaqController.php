<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        return view('customer.faq');
    }
}
