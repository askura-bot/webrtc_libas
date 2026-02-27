<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CredentialController extends Controller
{
    public function index()
    {
        $officer = User::where('role', 'officer')->first();
        $admin   = User::where('role', 'admin')->first();

        return view('admin.credentials', compact('officer', 'admin'));
    }

    public function updateOfficer(Request $request)
    {
        $request->validate([
            'officer_email'    => ['required', 'email'],
            'officer_password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = ['email' => $request->officer_email];

        if ($request->filled('officer_password')) {
            $data['password'] = Hash::make($request->officer_password);
        }

        User::where('role', 'officer')->update($data);

        return back()->with('success_officer', 'Kredensial petugas berhasil diperbarui.');
    }

    public function updateAdmin(Request $request)
    {
        $request->validate([
            'admin_email'    => ['required', 'email'],
            'admin_password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = ['email' => $request->admin_email];

        if ($request->filled('admin_password')) {
            $data['password'] = Hash::make($request->admin_password);
        }

        User::where('role', 'admin')->update($data);

        return back()->with('success_admin', 'Kredensial admin berhasil diperbarui.');
    }
}