<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('bookings')
            ->whereIn('role', ['owner', 'admin', 'staff', 'kasir', 'photographer', 'fotografer']);

        if ($request->filled('role') && $request->role !== 'all') {
            if ($request->role === 'owner') {
                $query->whereIn('role', ['owner', 'admin']);
            } elseif ($request->role === 'staff') {
                $query->whereIn('role', ['staff', 'kasir']);
            } elseif ($request->role === 'photographer') {
                $query->whereIn('role', ['photographer', 'fotografer']);
            } else {
                $query->where('role', $request->role);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $users = $query->latest()->paginate(12);

        $counts = [
            'all' => User::whereIn('role', ['owner', 'admin', 'staff', 'kasir', 'photographer', 'fotografer'])->count(),
            'owner' => User::whereIn('role', ['owner', 'admin'])->count(),
            'staff' => User::whereIn('role', ['staff', 'kasir'])->count(),
            'photographer' => User::whereIn('role', ['photographer', 'fotografer'])->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:owner,staff,photographer',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.required' => 'Peran (Role) wajib dipilih.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(Auth::id(), 'create_user', "Menambahkan anggota tim baru: {$user->name} ({$user->role_label})");

        return back()->with('success', "Anggota tim {$user->name} ({$user->role_label}) berhasil ditambahkan.");
    }

    public function updateRole(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id() && $request->role !== 'owner' && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan hak akses akun Anda sendiri.']);
        }

        $request->validate([
            'role' => ['required', Rule::in(['owner', 'admin', 'staff', 'kasir', 'photographer', 'fotografer', 'customer'])],
        ]);

        $oldRole = $user->role_label;
        $user->update(['role' => $request->role]);

        ActivityLog::record(Auth::id(), 'update_user_role', "Mengubah role {$user->name} dari {$oldRole} menjadi {$user->role_label}");

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$user->role_label}.");
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record(Auth::id(), 'delete_user', "Menghapus anggota tim: {$name}");

        return back()->with('success', "Anggota tim {$name} berhasil dihapus.");
    }
}
