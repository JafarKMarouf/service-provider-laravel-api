<?php

namespace App\Http\Controllers;

use App\Models\ExpertInfos;
use App\Models\CustomerInfos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            if (auth()->user()->role == 'expert') {
                $expert = ExpertInfos::query()->select(
                    'user_id',
                    'service_id',
                    'mobile',
                    'city',
                    'country',
                    'rating',
                    'description',
                    'working_hours',
                    'photo',
                    'price',
                    'updated_at',
                )
                    ->where('user_id', auth()->user()->id)
                    ->with('user:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $expert,
                ], 200);
            } else if (auth()->user()->role == 'customer') {
                $customer = CustomerInfos::query()->select(
                    'id',
                    'user_id',
                    'mobile',
                    'city',
                    'country',
                    'photo',
                    'updated_at',
                )
                    ->where('user_id', auth()->user()->id)
                    ->with('user:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $customer,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        // return $request->password;

        // return $request->all();
        try {
            $id = auth()->user()->id;

            $validate = Validator::make($request->all(), [
                'mobile' => 'digits:10',
                'user_id' => 'exists:users,id',
                'service_id' => 'exists:services,id',
                'name' => 'string|max:250',
                'email' => 'email|unique:users,email',
                'password' => 'string|min:8',
                'country' => 'string',
                'city' => 'string',
                'description' => 'string|min:25|max:60',
                'working_hours' => 'string',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg',
                'price' => 'string'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validate->errors(),
                ], 403);
            }

            $role = auth()->user()->role;
            $user = User::find($id);
            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'password' => $request->password != null ? Hash::make($request->password) : $user->password,
            ]);
            $filename = '';
            if ($role == 'expert') {
                $expert_id = ExpertInfos::where('user_id', $id)->value('id');
                $expert = ExpertInfos::find($expert_id);
                if ($request->photo != null) {
                    $image = $request->file('photo');
                    $filename = $this->uploadToImgBB($image);
                }
                $expert->update([
                    'mobile' => $request->mobile ?? $expert->mobile,
                    'country' => $request->country ?? $expert->country,
                    'city' => $request->city ?? $expert->city,
                    'working_hours' => $request->working_hours ?? $expert->working_hours,
                    'price' => $request->price ?? $expert->price,
                    'service_id' => $request->service_id ?? $expert->service_id,
                    'photo' => $filename != '' ? $filename : $expert->photo,
                ]);


                $expert = ExpertInfos::query()
                    ->select('id', 'user_id', 'service_id', 'mobile', 'country', 'city', 'description', 'rating', 'price', 'working_hours', 'photo', 'updated_at',)
                    ->where('user_id', $id)
                    ->with('user:id,name,email,role,updated_at')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' =>  $expert,
                    'message' => 'Expert Updated Successfully'
                ], 200);
            } else if ($role == 'customer') {
                $customer_id = CustomerInfos::where('user_id', $id)->value('id');
                $customer = CustomerInfos::find($customer_id);
                $filename = '';
                if ($request->photo != null) {
                    $image = $request->file('photo');
                    $filename = $this->uploadToImgBB($image);
                }

                $customer->update([
                    'photo' => $filename != '' ? $filename : $customer->photo,
                    'city' => $request->city ?? $customer->city,
                    'country' => $request->country ?? $customer->country,
                    'mobile' => $request->mobile ?? $customer->mobile,
                ]);

                $customer = CustomerInfos::query()->select(
                    'user_id',
                    'mobile',
                    'city',
                    'country',
                    'photo',
                    'updated_at',
                )
                    ->where('user_id', $id)
                    ->with('user:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $customer,
                    'message' => auth()->user()->name . ' Updated Successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy()
    {
        try {
            $role =  auth()->user()->role;
            $user = User::find(auth()->user()->id);
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => $role . ' deleted Successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
