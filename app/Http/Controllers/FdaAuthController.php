<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FdaAuthController extends Controller
{
    public function showLogin()
    {
        return view('fda.login'); // React entry point
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'userName' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $user = \App\Models\AdminUser::where('userName', $credentials['userName'])
        ->where('activated', 'Y')
        ->first();

    if ($user && $this->passwordMatches($user, $credentials['password'])) {
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

/**
 * Verify a submitted password against the stored value.
 *
 * Supports legacy plain-text passwords for backward compatibility and
 * transparently re-hashes them with bcrypt on the next successful login,
 * so stored credentials migrate to hashes over time without a data reset.
 */
protected function passwordMatches(\App\Models\AdminUser $user, string $password): bool
{
    $stored = (string) $user->password;

    // Already a bcrypt/argon hash: verify with a constant-time check.
    if (Hash::isHashed($stored)) {
        if (Hash::check($password, $stored)) {
            if (Hash::needsRehash($stored)) {
                $user->password = Hash::make($password);
                $user->save();
            }
            return true;
        }
        return false;
    }

    // Legacy plain-text value: compare in constant time, then upgrade to a hash.
    if (hash_equals($stored, $password)) {
        $user->password = Hash::make($password);
        $user->save();
        return true;
    }

    return false;
}


    public function logout(Request $request)
{
    Auth::guard('admin')->logout();
    
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Change this from return redirect('/admin/login');
    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
}

    public function dashboard()
{
    return view('fda.dashboard');
}

public function getUser()
{
    return response()->json([
        'user' => Auth::guard('admin')->user()
    ]);
}


}
