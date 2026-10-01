<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();

        return view('customer.packages.index', compact('packages', 'addons'));
    }
}
