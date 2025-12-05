<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 1;
    private const OTP_EXPIRY_SECONDS = 60;
    private const SESSION_EXPIRY_MINUTES = 5;

    // send otp
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'institution' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $otp = rand(100000, 999999);

        $registrationData = [
            'name' => $request->name,
            'email' => $request->email,
            'institution' => $request->institution,
            'password' => $request->password,
            'otp' => $otp,
        ];

        Cache::put('registration_' . $request->email, $registrationData, now()->addMinutes(self::SESSION_EXPIRY_MINUTES));

        try {
            Mail::raw("Kode OTP Anda adalah: {$otp}\n\nKode ini berlaku selama " . self::OTP_EXPIRY_MINUTES . " menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode Verifikasi OTP - Registrasi');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP telah dikirim ke email Anda',
                'email' => $request->email,
                'expires_in' => self::OTP_EXPIRY_SECONDS,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // verify otp
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $registrationData = Cache::get('registration_' . $request->email);

        if (!$registrationData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP sudah kadaluarsa atau tidak valid',
            ], 400);
        }

        if ($registrationData['otp'] != $request->otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode OTP tidak valid',
            ], 400);
        }

        $guestRole = DB::table('roles')->where('name', 'guest')->orWhere('name', 'user')->first();
        $roleId = $guestRole ? $guestRole->id : DB::table('roles')->min('id') ?? 5;

        $user = User::create([
            'full_name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'unit_name' => $registrationData['institution'],
            'password' => $registrationData['password'],
            'role_id' => $roleId,
            'email_verified_at' => now(),
        ]);

        Cache::forget('registration_' . $request->email);

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('role');

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil! Anda sekarang adalah guest.',
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'institution' => $user->unit_name,
                'role' => $user->role->name ?? 'guest',
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

        $registrationData = Cache::get('registration_' . $request->email);

        if (!$registrationData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi registrasi tidak ditemukan. Silakan daftar ulang.',
            ], 400);
        }

        $otp = rand(100000, 999999);
        $registrationData['otp'] = $otp;

        Cache::put('registration_' . $request->email, $registrationData, now()->addMinutes(self::SESSION_EXPIRY_MINUTES));

        try {
            Mail::raw("Kode OTP baru Anda adalah: {$otp}\n\nKode ini berlaku selama " . self::OTP_EXPIRY_MINUTES . " menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode Verifikasi OTP Baru - Registrasi');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP baru telah dikirim ke email Anda',
                'email' => $request->email,
                'expires_in' => self::OTP_EXPIRY_SECONDS,
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
