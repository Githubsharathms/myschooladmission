<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Signup;
use PragmaRX\Google2FA\Google2FA;
use Carbon\Carbon;

class UserDetailsController extends Controller
{
    public function index()
    {
        $users = Signup::all();
        return view('userdetails', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|unique:signup,mobile_number',
        ]);

        $google2fa = new Google2FA();
        $otp = $google2fa->generateSecretKey(32);
        $otpTimestamp = Carbon::now();

        $signup = new Signup();
        $signup->mobile_number = $request->mobile_number;
        $signup->otp = $otp;
        $signup->otp_timestamp = $otpTimestamp;
        $signup->first_request_timestamp = $otpTimestamp;
        $signup->otp_request_count = 1;
        $signup->save();

        return redirect()->route('userdetails')->with('success', 'User created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'mobile_number' => 'required|unique:signup,mobile_number,' . $id,
        ]);

        $signup = Signup::find($id);
        if (!$signup) {
            return redirect()->route('userdetails')->with('error', 'User not found!');
        }

        $signup->mobile_number = $request->mobile_number;
        $signup->name = $request->name;
        $signup->save();

        return redirect()->route('userdetails')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $signup = Signup::find($id);
        if ($signup) {
            $signup->delete();
            return redirect()->route('userdetails')->with('success', 'User deleted successfully!');
        }

        return redirect()->route('userdetails')->with('error', 'User not found!');
    }
}
