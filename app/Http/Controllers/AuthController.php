<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private function send_email_otp($user): array
    {
        try {
            $otp = random_int(100000, 999999);

            $user->update([
                'otp' => Hash::make($otp),
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::raw(
                "Your OTP is: {$otp}. It will expire in 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Email Verification OTP');
                }
            );

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
            ];

        } catch (\Throwable $e) {

            Log::error('OTP mail error', [
                'email' => $user->email ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Mail sending failed',
            ];
        }
    }
    public function register(UserRegisterRequest $request) {
        try {
            $fields = $request->validated();

            $fields['password'] = Hash::make($fields['password']); // hash na dilao choba

            $user = User::create($fields);

            $otpResult = $this->send_email_otp($user);

            if (!$otpResult['success']) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Failed to send OTP email',
                    'error'   => app()->isLocal()
                        ? $otpResult['error']   // FULL error (local/dev)
                        : null                  // hidden in production
                ], 500);
            }


            return apiSuccess('User created. OTP sent to email. Please verify your mail.',$user);

        } catch( \Throwable $e) {
            return apiError($e->getMessage(),500);
        }
    }

    public function login(UserLoginRequest $request) {

        try {
            $user = User::where('email', $request->email)->first();




            if (! $user || ! Hash::check($request->password, $user->password)) {
                return apiError('Invalid credentials', 401);
            }


            $token = $user->createToken('auth_token')->plainTextToken;

            return apiSuccess('Login successful', [
                'name'  => $user->name,
                'email' => $user->email,
                'token' => $token,
            ]);
        } catch(\Throwable $e) {
            return apiError($e->getMessage(),500);
        }

    }
}
