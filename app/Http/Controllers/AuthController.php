<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Ditambahkan
use Illuminate\Support\Facades\Auth; // Ditambahkan
use Illuminate\Support\Facades\Hash; // Ditambahkan 

class AuthController extends Controller
{
    public function signupForm()
    {
        $pageTitle = 'Signup Page';
        return view('auth.signup_form', ['pageTitle' => $pageTitle]);
    }

    public function signup(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => ['required', 'email', 'unique:users'],
                'password' => 'required',
            ],
            [
                'email.unique' => 'The email address is already taken.',
            ],
            $request->all()
        );

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return redirect()->route('home');
    }
}
