<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use App\Http\Resources\TaskResource;
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,

        ]);

        return redirect()->route('home');
        // return response()->json([
        //     'code' => 200,
        //     'message' => 'New user has been registered',
        //     'data' => $user,


        // ]);
    }

    public function loginForm()
    {
        $pageTitle = 'Login';
        return view('auth.login_form', ['pageTitle' => $pageTitle]);
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => 'required',
            ],
            $request->all()
        );

        $credentials = $request->only('email', 'password');

        // if (Auth::attempt($credentials)) {
        //     $token = $request->user()->createToken('Kanban-user-login');
            return redirect()->route('home');
            // return response()->json([
            //     'code' => 200,
            //     'message' => 'Login success!',
            //     'token' => $token->plainTextToken
            // ]);
        // }

        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        // $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
        return redirect()->route('auth.login');
        // return response()->json([
        //     'code' => 200,
        //     'message' => 'Logout success!',

        // ]);
    }
}
