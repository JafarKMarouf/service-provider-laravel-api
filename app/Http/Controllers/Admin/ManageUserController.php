<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManageUserController extends Controller
{
    public function index()
    {
        try {
            if (auth()->user()->role == 'admin') {

                $customers = User::query()
                            ->where('role', 'customer')
                            ->with('customerInfos')
                            ->orderBy('created_at', 'desc')->get();

                $experts = User::query()
                    ->where('role', 'expert')
                    ->with('expertInfos')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $data['count_experts'] = count($experts);
                $data['count_customers'] = count($customers);
                $data['experts'] = $experts;
                $data['customers'] = $customers;

                return response()->json([
                    'data' => $data
                ],200);

            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            if (auth()->user()->role == 'admin') {
                $user = User::query()->find($id);
                if (!$user) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'user not found'
                    ], 403);
                }
                // $user = $user->role;
                if($user->role == 'customer'){
                    $user = $user->where('id',$id)->with('customerInfos')->get();
                }else{
                    $user = $user->where('id',$id)->with('expertInfos')->get();
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $user,
                ],200);

            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if (auth()->user()->role == 'admin') {
                $validate = Validator::make($request->all(), [
                    'role' => ['required'],
                ]);
                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors()->first(),
                    ], 403);
                }

                $user = User::find($id);
                $role = User::query()
                    ->where('id', $id)
                    ->where('role', '!=', 'admin')
                    ->get();
                if (empty($role)) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'user not found or role is admin and can not change it'
                    ], 403);
                }

                $role_user =  $user->role;

                if ($role_user == $request->role) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'the role of current user is same role of update role'

                    ], 403);
                }
                $user->update([
                    'role' => $request->role,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $user,
                    'message' => $role_user . ' become ' . $request->role . ' Successfully'
                ],200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        // return $id;
        try {
            if (auth()->user()->role != 'admin') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
            if (!empty(User::find($id))) {

                $delete = User::query()
                    ->where('id', $id)
                    ->delete();
                if ($delete) {
                    return response()->json([
                        'status' => 'success',
                        'user' => $delete,
                        'message' => 'User Deleted Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Could not delete admin'
                    ],403);
                }
            }
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
