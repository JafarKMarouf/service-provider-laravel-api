<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(){
        try{
            if(auth()->user()->role == 'expert'){
                $service = Service::query()
                    ->where('expert_id',auth()->user()->id)
                    ->with('category:id,title,description')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'count' => count($service),
                    'data' => $service,
                ],200);
            }
            $service = Service::query()
                ->with('category:id,title,description')
                ->with('expert:id,name','expert.expertInfos:expert_id,mobile,country,city,certificate')
                ->get();
            return response()->json([
                'status' => 'success',
                'count' => count($service),
                'data' => $service,
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function store(Request $request)
    {
        try{
            if(auth()->user()->role == 'expert'){
                $validate = Validator::make($request->all(),[
                    'user_id' => 'exists:users,id|',
                    'category_id' => 'required|exists:categories,id',
                    'service_name' => 'required|string|max:25|unique:services',
                    'service_description' => 'required|string|min:20',
                    'price' => 'required',
                    'work_time' => 'required',
                    'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);

                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors(),
                    ], 403);
                }
                $filename = '';
                $file = str($request->service_name.'_'.auth()->user()->name)->lower()->remove(' ');
                if ($request->photo!=null) {
                    $photo = $request->file('photo');
                    $filename = $file . '_' . time() . '.' .$photo->getClientOriginalExtension();
                    $photo->storeAs('public/services/photos', $filename);
                }
                // return $filename;
                $service = Service::create([
                    'expert_id' => auth()->user()->id,
                    'category_id' => $request->category_id,
                    'service_name' => $request->service_name,
                    'service_description' => $request->service_description,
                    'price' => $request->price,
                    'photo' => $filename,
                    'work_time' => $request->work_time,
                ]);
                return response()->json([
                    'status' => 'success',
                    'data' => $service,
                    'message' => 'service is Created Successfully!'
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
    public function show($id){
        try{
            if(empty(Service::find($id))){
                return response()->json([
                    'status'=> 'failed',
                    'message' => 'Service is not found!'
                ],403);
            }

            if(auth()->user()->role == 'expert'){

                $service = Service::query()
                    ->where('id', $id)
                    ->where('expert_id',auth()->user()->id)
                    ->with('category:id,title,description')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    // 'count' => count($service),
                    'data' => $service,
                ],200);
            }
            $service = Service::query()
                ->where('id', $id)
                ->with('category:id,title,description')
                ->with('expert:id,name,email','expert.expertInfos:expert_id,mobile,country,city,rating,description,certificate,photo')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $service,
            ],200);

        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }


    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
