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
                    'user_id',
                    'mobile',
                    'city',
                    'country',
                    'photo',
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
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

            if ($request->name != null) {
                $user->name = $request->name;
            }
            if ($request->password != null) {
                $user->password = Hash::make($request->password);
            }
            if ($request->email != null) {
                $user->email = $request->email;
            }
            if ($role == 'expert') {
                $expert_id = ExpertInfos::where('user_id', $id)->value('id');
                $expert = ExpertInfos::find($expert_id);
                if ($request->photo != null) {
                    $ext = $request->file('photo')->getClientOriginalExtension();
                    $filename = $this->saveImage($request->photo, $ext, 'experts');
                    $expert->photo = $filename;
                    $expert->save();
                }
                if ($request->mobile != null) {
                    $expert->mobile = $request->mobile;
                }

                if ($request->country != null) {
                    $expert->country = $request->country;
                }

                if ($request->city != null) {
                    $expert->city = $request->city;
                }

                if ($request->working_hours != null) {
                    $expert->working_hours = $request->working_hours;
                }
                if ($request->price != null) {
                    $expert->price = $request->price;
                }
                if ($request->service_id != null) {
                    $expert->service_id = $request->service_id;
                }

                $user->save();
                $expert->save();
                $expert = ExpertInfos::query()
                    ->select('id', 'user_id', 'service_id', 'mobile', 'country', 'city', 'description', 'rating', 'price', 'working_hours', 'photo')
                    ->where('user_id', $id)
                    ->with('user:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' =>  $expert,
                    'message' => 'Expert Updated Successfully'
                ], 200);
            } else if ($role == 'customer') {
                $customer_id = CustomerInfos::where('user_id', $id)->value('id');
                $customer = CustomerInfos::find($customer_id);

                if ($request->photo != null) {
                    $ext = $request->file('photo')->getClientOriginalExtension();
                    $filename = $this->saveImage($request->photo, $ext, 'customers');
                    $customer->photo = $filename;
                }

                if ($request->mobile != null) {
                    $customer->mobile = $request->mobile;
                }

                if ($request->country != null) {
                    $customer->country = $request->country;
                }

                if ($request->city != null) {
                    $customer->city = $request->city;
                }

                $user->save();
                $customer->save();
                $customer = CustomerInfos::query()->select(
                    'customer_id',
                    'mobile',
                    'city',
                    'country',
                    'photo',
                )
                    ->where('user_id', $id)
                    ->with('user:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $customer,
                    'message' => 'Customer Updated Successfully'
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
