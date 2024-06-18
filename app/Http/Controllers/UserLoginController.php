<?php

// app/Http/Controllers/UserLoginController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signup;
use PragmaRX\Google2FA\Google2FA;
use Carbon\Carbon;

class UserLoginController extends Controller
{
    public function submitMobile(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
        ]);

        // Check if the mobile number is registered
        $signup = Signup::where('mobile_number', $request->mobile_number)->first();

        if (!$signup || !$signup->name) {
            return response()->json(['error' => 'Please sign up first.']);
        }

        // Generate OTP
        $google2fa = new Google2FA();
        $otp = $google2fa->generateSecretKey(32);
        $otpTimestamp = Carbon::now();

        // Save OTP data in session
        session([
            'mobile_number' => $request->mobile_number,
            'otp' => $otp,
            'otp_timestamp' => $otpTimestamp,
        ]);

        return response()->json([
            'otp' => $otp,
            'step' => 2
        ]);
    }

    public function submitOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        // Get OTP data from session
        $sessionData = session()->all();
        $otpTimestamp = Carbon::parse($sessionData['otp_timestamp']);
        $currentTimestamp = Carbon::now();

        // Check if OTP has expired
        if ($currentTimestamp->diffInSeconds($otpTimestamp) > 60) {
            return response()->json(['error' => 'OTP has expired. Please request a new OTP.']);
        }

        // Check if OTP is correct
        if ($sessionData['otp'] === $request->otp) {
            return response()->json(['message' => 'Login successful!', 'step' => 3]);
        }

        return response()->json(['error' => 'Invalid OTP']);
    }
}
