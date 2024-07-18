<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExpertInfos;
use App\Models\Service;
use App\Models\User;
use Google\Service\CloudFunctions\Retry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $service = Service::query()
                ->with('category:id,title,description,photo')
                ->with('expert:user_id,service_id,mobile,country,city,rating,price,photo,working_hours', 'expert.user:id,name')
                ->get(['id', 'category_id', 'service_name', 'service_description', 'photo']);
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
            if (auth()->user()->role == 'admin') {
                $validate = Validator::make($request->all(), [
                    'category_id' => 'required|exists:categories,id',
                    'service_name' => 'required|string|max:25|unique:services',
                    'service_description' => 'required|string|min:20',
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
                    'category_id' => $request->category_id,
                    'service_name' => $request->service_name,
                    'service_description' => $request->service_description,
                    'photo' => $filename,
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
            $service = Service::query()
                ->where('id', $id)
                ->with('category:id,title,description')
                ->with('expert:user_id,service_id,mobile,country,city,rating,price,photo,working_hours', 'expert.user:id,name')
                ->get(['id', 'category_id', 'service_name', 'service_description', 'photo']);
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

            $validate = Validator::make($request->all(), [
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
        try {
            if (auth()->user()->role == 'admin') {
                $service = Service::find($id);
                if (!$service) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Service not found'
                    ], 403);
                }
                $service->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Service deleted Succfully!',
                ]);
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

    public function serviceByCategory($category_id)
    {
        try {
            $service = Service::query()
                ->where('category_id', $category_id)
                ->with('category:id,title,description,photo')
                ->with('expert:user_id,service_id,mobile,country,city,rating,price,photo,working_hours', 'expert.user:id,name')
                ->get(['id', 'category_id', 'service_name', 'service_description', 'photo']);
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

    public function fetchExpertsForService($service_id)
    {
        try {
            $experts = Service::query()
                ->find($service_id)
                ->expert;
            return response()->json([
                'status' => 'success',
                'count' => count($experts),
                'data' => $experts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
