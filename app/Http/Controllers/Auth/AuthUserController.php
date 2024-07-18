<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerInfos;
use App\Models\ExpertInfos;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthUserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:250',
                'email' => 'required|unique:users,email|email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            $expert = new ExpertInfos();
            if ($request->role == 'expert') {
                $expert->create([
                    'expert_id' => $user->id,
                ]);
            }
            $customer = new CustomerInfos();
            if ($request->role == 'customer') {
                $customer->create([
                    'customer_id' => $user->id,
                ]);
            }

            $data['token'] = $user->createToken(
                $request->email,
                [$request->role],
                now()->addWeek()
            )->plainTextToken;
            $data['user'] = $user;

            $user->notify(new EmailVerificationNotification());

            $response = [
                'status' => 'success',
                'data' => $data,
                'message' => $request->role . ' is Created successfully, a verification code has been sent to your email',
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'false',
                'data' => [],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Validation Error!',
                    'data' => $validate->errors(),
                ], 403);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'user ' . $request->email . ' not exists.'
                ], 403);
            }
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $data['token'] = $user->createToken(
                $request->email,
                [$user->role],
                now()->addWeek()
            )->plainTextToken;
            $data['user'] = $user;
            $response = [
                'status' => 'success',
                'data' => $data,
                'message' => $user->role . ' is logged in successfully.',
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'false',
                'data' => [],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        $id = auth()->user()->id;
        User::find($id)->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User is logged out successfully'
        ], 200);
    }

    public function getEmailUser($user_id)
    {
        if (auth()->user()->id != $user_id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Persmission Denied'
            ], 403);
        }
        $user = User::find($user_id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
            ],
            'message' => 'fetch email of user',
        ]);
    }
}
