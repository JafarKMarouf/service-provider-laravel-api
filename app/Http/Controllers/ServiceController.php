<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Google\Service\CloudFunctions\Retry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            if (auth()->user()->role == 'expert') {
                $service = Service::query()
                    ->where('expert_id', auth()->user()->id)
                    ->with('category:id,title,description,photo')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'count' => count($service),
                    'data' => $service,
                ], 200);
            }
            $service = Service::query()
                ->with('category:id,title,description,photo')
                ->with('expert:id,name', 'expert.expertInfos:expert_id,mobile,country,city,certificate,rating,description,working_hours,photo')
                ->get();
            return response()->json([
                'status' => 'success',
                'count' => count($service),
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            if (auth()->user()->role == 'expert') {
                $validate = Validator::make($request->all(), [
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

                $ext = $request->file('photo')->getClientOriginalExtension();
                $filename = $this->saveImage($request->photo, $ext, 'services');

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
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            if (empty(Service::find($id))) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Service is not found!'
                ], 403);
            }

            if (auth()->user()->role == 'expert') {
                $expert_id = Service::where('id', $id)->value('expert_id');

                if ($expert_id != auth()->user()->id) {
                    return response()->json([
                        'success' => 'failed',
                        'message' => 'Permission deined',
                    ], 401);
                }
                $service = Service::query()
                    ->where('id', $id)
                    ->with('category:id,title,description,photo')
                    ->get();

                return response()->json([
                    'status' => 'success',
                    // 'count' => count($service),
                    'data' => $service,
                ], 200);
            }
            $service = Service::query()
                ->where('id', $id)
                ->with('category:id,title,description')
                ->with('expert:id,name,email', 'expert.expertInfos:expert_id,mobile,country,city,rating,description,certificate,photo')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, int $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Service not found'
                ], 404);
            }
            $expert_id = Service::where('id', $id)->value('expert_id');

            if ($expert_id != auth()->user()->id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission deined',
                ], 401);
            }

            $validate = Validator::make($request->all(), [
                'user_id' => 'exists:users,id|',
                'category_id' => 'exists:categories,id',
                'service_name' => 'string|max:25|unique:services',
                'service_description' => 'string|min:20',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validate->errors(),
                ], 403);
            }

            if ($request->hasFile('photo')) {
                $ext = $request->file('photo')->getClientOriginalExtension();
                $filename = $this->saveImage($request->photo, $ext, 'services');
                $service->photo = $filename;
            }

            if ($request->service_name != null) {
                $service->service_name = $request->service_name;
            }

            if ($request->service_description != null) {
                $service->service_description = $request->service_description;
            }

            if ($request->category_id != null) {
                $service->category_id = $request->category_id;
            }

            if ($request->price != null) {
                $service->price = $request->price;
            }
            if ($request->work_time != null) {
                $service->work_time = $request->work_time;
            }

            $service->save();

            return response()->json([
                'status' => 'success',
                'service' => $service,
                'message' => 'Service udpated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }

    public function serviceForCategory($category_id)
    {
        try {
            $service = Service::query()
                ->where('category_id', $category_id)
                ->with('category:id,title,description,photo')
                ->with('expert:id,name', 'expert.expertInfos:expert_id,mobile,country,city,certificate,rating,description,working_hours,photo')
                ->get();
            return response()->json([
                'status' => 'success',
                'count' => count($service),
                'data' => $service,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
