<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Request;

class EmailVerificationController extends Controller
{
    private $otp;
    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function sendVerificationEmail(Request $request)
    {
        try {
            $id = auth()->user()->id;
            User::find($id)->notify(new EmailVerificationNotification());
            return response()->json([
                'status' => 'success',
                'message' => 'Sent Code OTP Successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function emailVerification(EmailVerificationRequest $request)
    {
        try {
            $otp2 = $this->otp->validate($request->email, $request->otp);
            if (!$otp2->status) {
                return response()->json([
                    "status" => 'false',
                    'message ' => $otp2,
                ], 401);
            }
            $user = User::where('email', $request->email)->first();
            $user->email_verified_at = now();
            $user->save();
            return response()->json([
                'status' => 'true',
                'message' => 'Email Verified Successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
