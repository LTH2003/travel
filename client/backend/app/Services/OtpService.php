<?php

namespace App\Services;

use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpCodeMail;
use Carbon\Carbon;

class OtpService
{
    protected const OTP_LENGTH = 6;
    protected const OTP_EXPIRATION_MINUTES = 10;

    /**
     * Generate and send OTP code to user's email
     */
    public function sendOtp(User $user): bool
    {
        try {
            // Delete any existing unused OTP codes for this user
            $user->otpCodes()->where('used', false)->delete();

            // Generate new OTP code
            $code = $this->generateOtpCode();

            // Create OTP record
            $otpCode = $user->otpCodes()->create([
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRATION_MINUTES),
                'used' => false,
            ]);

            // Send OTP email
            Mail::to($user->email)->send(new OtpCodeMail($user, $code));

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP code for user
     */
    public function verifyOtp(User $user, string $code): bool
    {
        $otpCode = $user->otpCodes()
            ->where('code', $code)
            ->where('used', false)
            ->first();

        if (!$otpCode) {
            return false;
        }

        // Check if OTP is expired
        if (!$otpCode->isValid()) {
            return false;
        }

        // Mark as used
        $otpCode->update(['used' => true]);

        return true;
    }

    /**
     * Resend OTP with rate limiting
     */
    public function resendOtp(User $user): bool
    {
        // Check if user has an active OTP
        $activeOtp = $user->otpCodes()
            ->where('used', false)
            ->first();

        if ($activeOtp) {
            // Check if less than 30 seconds have passed
            $timeDiff = Carbon::now()->diffInSeconds($activeOtp->created_at);
            if ($timeDiff < 30) {
                throw new \Exception('Please wait before requesting a new OTP');
            }
        }

        return $this->sendOtp($user);
    }

    /**
     * Generate a random 6-digit OTP code
     */
    protected function generateOtpCode(): string
    {
        return str_pad(random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Cleanup expired OTP codes
     */
    public function cleanupExpiredCodes(): void
    {
        OtpCode::where('expires_at', '<', Carbon::now())
            ->where('used', false)
            ->delete();
    }
}
