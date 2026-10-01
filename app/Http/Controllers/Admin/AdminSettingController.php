<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'open_time' => Setting::getByKey('open_time', '09:00'),
            'close_time' => Setting::getByKey('close_time', '21:00'),
            'buffer_minutes' => Setting::getByKey('buffer_minutes', 15),
            'dp_percent' => Setting::getByKey('dp_percent', 50),
            'extra_person_fee' => Setting::getByKey('extra_person_fee', 25000),
            'studio_address' => Setting::getByKey('studio_address', 'Jl. Tunjungan No. 88, Surabaya'),
            'studio_phone' => Setting::getByKey('studio_phone', '+62 812-3456-7890'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::setKey($key, $value);
        }

        ActivityLog::record(request()->user()?->id, 'admin_update_settings', 'Memperbarui pengaturan sistem studio.');

        return back()->with('success', 'Pengaturan studio berhasil diperbarui.');
    }
}
