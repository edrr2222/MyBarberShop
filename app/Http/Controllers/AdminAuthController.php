<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['usuario' => 'required|string', 'password' => 'required|string']);

        $admin = Admin::where('usuario', $request->usuario)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['usuario' => 'Usuario o contraseña incorrecta.']);
        }

        auth('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();
        $request->session()->invalidate();

        return redirect()->route('admin.login');
    }
}
