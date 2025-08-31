<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    /**
     * Send password reset email to user.
     */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60); // Token expires in 1 hour

        // Store reset token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // Generate reset URL (assuming frontend reset password route)
        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        // Send reset email
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($resetUrl, $user->name));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link has been sent to your email address.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset email. Please try again later.',
            ], 500);
        }
    }

    /**
     * Reset user password with token.
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if token exists and is valid
        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', hash('sha256', $request->token))
            ->where('created_at', '>', now()->subMinutes(60)) // Token expires after 1 hour
            ->first();

        if (!$resetToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
            ], 422);
        }

        // Update user password
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
