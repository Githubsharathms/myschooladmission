<?php

// app/Http/Controllers/SignupController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signup;
use PragmaRX\Google2FA\Google2FA;
use Carbon\Carbon;

class SignupController extends Controller
{
    public function submitMobile(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
        ]);

        // Check if the mobile number is already taken
        $signup = Signup::where('mobile_number', $request->mobile_number)->first();

        if ($signup && $signup->name) {
            return response()->json(['error' => 'This number is already taken.']);
        }

        // Generate OTP
        $google2fa = new Google2FA();
        $otp = $google2fa->generateSecretKey(32);
        $otpTimestamp = Carbon::now();

        // Store data in session
        $request->session()->put('signup', [
            'mobile_number' => $request->mobile_number,
            'otp' => $otp,
            'otp_timestamp' => $otpTimestamp,
            'first_request_timestamp' => $otpTimestamp,
            'otp_request_count' => 1
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

        // Retrieve data from session
        $signupData = $request->session()->get('signup');

        if (!$signupData) {
            return response()->json(['error' => 'Session expired. Please start again.']);
        }

        $otpTimestamp = Carbon::parse($signupData['otp_timestamp']);
        $currentTimestamp = Carbon::now();

        // Check if OTP has expired
        if ($currentTimestamp->diffInSeconds($otpTimestamp) > 60) {
            return response()->json(['error' => 'OTP has expired. Please request a new OTP.']);
        }

        // Check if OTP is correct
        if ($signupData['otp'] === $request->otp) {
            return response()->json(['step' => 3]);
        }

        return response()->json(['error' => 'Invalid OTP']);
    }

    public function submitName(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // Retrieve data from session
        $signupData = $request->session()->get('signup');

        if (!$signupData) {
            return response()->json(['error' => 'Session expired. Please start again.']);
        }

        // Save data to the database
        $signup = new Signup();
        $signup->mobile_number = $signupData['mobile_number'];
        $signup->name = $request->name;
        $signup->otp = null; // Clear the OTP after successful verification
        $signup->otp_timestamp = $signupData['otp_timestamp'];
        $signup->first_request_timestamp = $signupData['first_request_timestamp'];
        $signup->otp_request_count = $signupData['otp_request_count'];
        $signup->save();

        // Clear session data
        $request->session()->forget('signup');

        return response()->json(['message' => 'Signup successful!', 'step' => 4]);
    }
}
