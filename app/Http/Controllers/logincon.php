<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class logincon extends Controller
{

public function signup(Request $request)
{
    $validated = $request->validate([
        'username' => 'required|unique:users,username|min:3|max:50',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    Auth::login($user); // ⬅️ variabel $user sekarang sudah didefinisikan

    return redirect('/tasks');
}


    public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if (Auth::attempt([
        'email' => $validated['email'],
        'password' => $validated['password'],
    ])) {
        $request->session()->regenerate(); // ⬅️ WAJIB agar login sah

        return redirect()->intended('/tasks'); // pakai intended agar redirect aman
    }

    return redirect()->route('signin')->withErrors([
        'email' => 'Email atau password salah.',
    ]);
}
}