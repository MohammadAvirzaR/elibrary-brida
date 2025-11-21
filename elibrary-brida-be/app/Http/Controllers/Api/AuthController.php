<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // REGISTER - Step 1: Kirim OTP
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'institution' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Generate OTP 6 digit
        $otp = rand(100000, 999999);
        
        // Simpan data registrasi dan OTP di cache (expired 10 menit)
        $registrationData = [
            'name' => $request->name,
            'email' => $request->email,
            'institution' => $request->institution,
            'password' => $request->password,
            'otp' => $otp,
        ];
        
        Cache::put('registration_' . $request->email, $registrationData, now()->addMinutes(10));

        // Kirim OTP via email
        try {
            Mail::raw("Kode OTP Anda adalah: {$otp}\n\nKode ini berlaku selama 10 menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode Verifikasi OTP - Registrasi');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP telah dikirim ke email Anda',
                'email' => $request->email,
                'expires_in' => 600, // 10 menit dalam detik
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // VERIFY OTP - Step 2: Verifikasi OTP dan buat user
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        // Ambil data registrasi dari cache
        $registrationData = Cache::get('registration_' . $request->email);

        if (!$registrationData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP sudah kadaluarsa atau tidak valid',
            ], 400);
        }

        // Verifikasi OTP
        if ($registrationData['otp'] != $request->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP tidak valid',
            ], 400);
        }

        // Buat user baru dengan role contributor (role_id = 4)
        $user = User::create([
            'full_name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'unit_name' => $registrationData['institution'],
            'password' => $registrationData['password'],
            'role_id' => 4, // reviewer role
            'email_verified_at' => now(), // Set email as verified
        ]);

        // Hapus data registrasi dari cache
        Cache::forget('registration_' . $request->email);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil! Anda sekarang adalah kontributor.',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'institution' => $user->unit_name,
                'role' => $user->role->name ?? 'contributor',
            ],
            'token' => $token,
        ], 201);
    }

    // RESEND OTP - Kirim ulang OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cek apakah ada data registrasi di cache
        $registrationData = Cache::get('registration_' . $request->email);

        if (!$registrationData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi registrasi tidak ditemukan. Silakan daftar ulang.',
            ], 400);
        }

        // Generate OTP baru
        $otp = rand(100000, 999999);
        $registrationData['otp'] = $otp;

        // Update cache dengan OTP baru
        Cache::put('registration_' . $request->email, $registrationData, now()->addMinutes(10));

        // Kirim OTP via email
        try {
            Mail::raw("Kode OTP baru Anda adalah: {$otp}\n\nKode ini berlaku selama 10 menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode Verifikasi OTP Baru - Registrasi');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP baru telah dikirim ke email Anda',
                'email' => $request->email,
                'expires_in' => 600,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ], 500);
        }
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username ?? $user->full_name,
                'institution' => $user->unit_name,
                'role' => $user->role->name ?? 'guest',
            ],
            'token' => $token,
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // PROFILE (ME)
    public function me(Request $request)
    {
        $user = $request->user();

        // Load role relationship
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username ?? $user->full_name,
                'institution' => $user->unit_name,
                'unit_name' => $user->unit_name,
                'role' => [
                    'id' => $user->role->id ?? null,
                    'name' => $user->role->name ?? 'guest',
                ],
            ],
        ]);
    }
}