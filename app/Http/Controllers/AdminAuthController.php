<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login'); // React entry point
    }

    public function login(Request $request)
{
    $user = \App\Models\AdminUser::where('userName', $request->userName)
        ->where('activated', 'Y')
        ->first();

    if ($user && $user->password === $request->password) { // plain text comparison
        Auth::guard('admin')->login($user); // manually log in
        $request->session()->regenerate();
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials or user not activated.'
    ], 401);
}


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard()
{
    return view('admin.dashboard');
}

public function getUser()
{
    return response()->json([
        'user' => Auth::guard('admin')->user()
    ]);
}


}
