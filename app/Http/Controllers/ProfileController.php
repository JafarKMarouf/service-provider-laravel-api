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
    public function show(string $user_id)
    {
        try{
            if(auth()->user()->id != $user_id){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Persmission Denied'
                ],403);
            }
            if(auth()->user()->role == 'expert'){
                $expert = ExpertInfos::query()->select(
                    'expert_id',
                    'mobile',
                    'city',
                    'country',
                    'rating',
                    'description',
                    'working_hours',
                    'photo',
                    'certificate')
                ->where('expert_id' ,$user_id)
                ->with('expert:id,name,email,role')
                ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $expert,
                ],200);
            }
            else if(auth()->user()->role == 'customer'){
                $customer = CustomerInfos::query()->select(
                    'customer_id',
                    'mobile',
                    'city',
                    'country',
                    'photo',)
                ->where('customer_id' ,$user_id)
                ->with('customer:id,name,email,role')
                ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $customer,
                ],200);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function update(Request $request,$user_id)
    {
        // return $request;
        try{
            $id = auth()->user()->id;
            if($user_id != $id){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ],403);
            }

            $validate = Validator::make($request->all(), [
                'mobile' => 'digits:10',
                'expert_id' => 'exists:expert_infos,expert_id',
                'customer_id' => 'exists:customer_infos,customer_id',
                'name' => 'string|max:250',
                'email' => 'email|unique:users,email',
                'password' => 'string|min:8',
                'country' => 'string',
                'city' => 'string',
                'description' => 'string|min:25|max:60',
                'certificate' => 'string',
                'working_hours' => 'string',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'false',
                    'message' => $validate->errors(),
                ],403);
            }


            $role = User::find($id)->role;
            $user = User::find($user_id);
            // return $user;

            if ($request->name != null) {
                $user->name = $request->name;
            }
            if ($request->password != null) {
                $user->password = Hash::make($request->password);
            }
            if ($request->email != null) {
                $user->email = $request->email;
            }
            if($role == 'expert'){
                $expert_id = ExpertInfos::where('expert_id',$user_id)->value('id');
                $expert = ExpertInfos::find($expert_id);
                if ($request->photo!=null) {
                    $photo = $request->file('photo');
                    $filename = trim($user->name) . '_' . time() . '.' .
                    $photo->getClientOriginalExtension();
                    $photo->storeAs('public/'.$role.'/photos', $filename);
                    $expert->photo = $filename;
                    $expert->save();
                }
                // return $request->mobile;
                if ($request->mobile != null) {
                    $expert->mobile = $request->mobile;
                }

                if ($request->country != null) {
                    $expert->country = $request->country;
                }

                if ($request->city != null) {
                    $expert->city = $request->city;
                }

                if ($request->certificate != null) {
                    $expert->certificate = $request->certificate;
                }

                if ($request->working_hours != null) {
                    $expert->working_hours = $request->working_hours;
                }
                    // ExpertInfos::find($expert_id)->update([
                    //     'mobile' => $request->mobile,
                    //     'city' => $request->city,
                    // ]);
                $user->save();
                $expert->save();

                return response()->json([
                    'status' => 'success' ,
                    'data' => [
                        'user' => $user,
                        'expert_info' => $expert,
                    ],
                    'message' => 'Expert Updated Successfully'
                ],200);
            }

            else if ($role == 'customer'){
                $customer_id = CustomerInfos::where('customer_id',$user_id)->value('id');
                $customer = CustomerInfos::find($customer_id);

                if ($request->photo!=null) {
                    $photo = $request->file('photo');
                    $filename = trim($user->name) . '_' . time() . '.' .
                    $photo->getClientOriginalExtension();
                    $photo->storeAs('public/'.$role.'/photos', $filename);
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

                return response()->json([
                    'status' => 'success' ,
                    'data' => [
                        'user' => $user,
                        'expert_info' => $customer,
                    ],
                    'message' => 'Customer Updated Successfully'
                ],200);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }
    public function destroy(string $user_id)
    {
        try{
            if(auth()->user()->id != $user_id){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Persmission Denied'
                ],403);
            }
            $user = User::find($user_id);
            $user->delete();
            return response()->json([
                'status' => 'success' ,
                'message' => 'User deleted Successfully'
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }
}
