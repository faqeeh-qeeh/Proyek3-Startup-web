<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClientResource;
class ClientAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.client.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:clients',
            'email' => 'required|string|email|max:255|unique:clients',
            'whatsapp_number' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'address' => 'required|string|max:500',
            'birth_date' => 'required|date',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $client = Client::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
        ]);

        // Auth::guard('client')->login($client);

        return view('auth.client.login')->with('success', 'Registration successful! Please login.');
    }

    public function showLoginForm()
    {
        return view('auth.client.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::guard('client')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->route('client.products.index');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login');
    }






    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:clients',
            'email' => 'required|string|email|max:255|unique:clients',
            'whatsapp_number' => 'required|string|max:20',
            'gender' => 'required|in:male,female',
            'address' => 'required|string|max:500',
            'birth_date' => 'required|date',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $client = Client::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
            'gender' => $request->gender,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'password' => Hash::make($request->password),
        ]);

        $token = $client->createToken('client-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'client' => new ClientResource($client),
            'token' => $token
        ], 201);
    }

    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::guard('client')->attempt([
            $loginType => $request->login,
            'password' => $request->password
        ])) {
            $client = Auth::guard('client')->user();
            $token = $client->createToken('client-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'client' => new ClientResource($client),
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function apiLogout(Request $request)
    {
        $request->user('client-api')->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function getUser(Request $request)
    {
        return response()->json(new ClientResource($request->user('client-api')));
    }
}