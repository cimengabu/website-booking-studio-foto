<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminCustomerController extends Controller
{
    public function index()
    {
        $customers = User::withCount('bookings')
            ->where('role', 'customer')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }
}
