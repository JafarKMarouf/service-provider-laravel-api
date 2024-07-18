<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BookService;
use App\Models\ExpertInfos;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingServiceController extends Controller
{
    public function index()
    {
        try {
            if (auth()->user()->role == 'admin') {
                $book_service = BookService::query()
                    ->with('customer:id,mobile,country,city,photo')
                    ->with(
                        'service:id,service_name,photo',
                        'service.expert:id,service_id,mobile,country,city,photo,rating,price,working_hours'
                    )
                    ->get(['id', 'customer_id', 'service_id', 'description', 'delivery_time', 'status']);
                return response()->json([
                    'status' => 'success',
                    'count' => count($book_service),
                    'data' => $book_service,
                ], 200);
            }

            // there is problem you must solve it
            if (auth()->user()->role == 'customer') {
                $count = BookService::query()
                    ->where('customer_id', auth()->user()->id)
                    ->count();
                if (!$count > 0) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Not Found any book service for you',
                    ], 404);
                }
                $customer = BookService::query()
                    ->where('customer_id', auth()->user()->id)
                    ->value('customer_id');
                if ($customer != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $book_service = BookService::query()
                    ->where('customer_id', $customer)
                    ->with('service:id,service_name,photo,price,expert_id', 'service.expert:id,name,email,role', 'service.expert.expertInfos')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'count ' => count($book_service),
                    'data' => $book_service
                ], 200);
            }

            if (auth()->user()->role == 'expert') {
                $expert_id =  auth()->user()->id;

                $service_id = ExpertInfos::query()
                    ->where('user_id', $expert_id)
                    ->value('service_id');

                $book_service = [];
                $book_service_for_expert = BookService::query()
                    ->where('service_id', $service_id)
                    ->with('customer:id,user_id,mobile,country,city,photo', 'customer.user:id,name,email')
                    ->with('service:id,service_name,photo')
                    ->get(['id', 'customer_id', 'service_id', 'description', 'delivery_time', 'status']);

                return response()->json([
                    'status' => 'success',
                    'count' => count($book_service_for_expert),
                    'data' => $book_service_for_expert
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request, $service_id)
    {
        try {
            if (auth()->user()->role != 'admin') {
                if (empty(Service::find($service_id))) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'service is not found'
                    ], 403);
                }
                $validate = Validator::make($request->all(), [
                    'customer_id' => 'exists:users,id',
                    'service_id' => 'exists:services,id',
                    'description' => 'string|min:20',
                    'delivery_time' => 'string'
                ]);
                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors(),
                    ], 403);
                }

                $book = BookService::create([
                    'customer_id' => auth()->user()->id,
                    'service_id' => $service_id,
                    'description' => $request->description,
                    'delivery_time' => $request->delivery_time,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $book,
                    'message' => 'Booking a service created Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'admin can not book a service :)'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $book_id)
    {
        try {
            if (empty(BookService::find($book_id))) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Book service is not found',
                ]);
            }
            if (auth()->user()->role == 'customer') {
                $customer = BookService::query()->find($book_id)->customer->id;
                if ($customer != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $book_service = BookService::query()
                    ->where('id', $book_id)
                    ->with('service:id,service_name,photo,price,expert_id', 'service.expert:id,name,email,role', 'service.expert.expertInfos')
                    // ->with('customer:id,name,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $book_service
                ], 200);
            } else if (auth()->user()->role == 'expert') {
                $expert = BookService::query()
                    ->find($book_id)
                    ->service
                    ->expert
                    ->value('user_id');
                if ($expert != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }

                $book_service = BookService::query()
                    ->where('id', $book_id)
                    ->with('customer:id,user_id,mobile,country,city,photo', 'customer.user:id,name,email')
                    ->with('service:id,service_name,photo')
                    ->get(['id', 'customer_id', 'service_id', 'description', 'delivery_time', 'status']);

                return response()->json([
                    'status' => 'success',
                    'data' => $book_service
                ], 200);
            } else if (auth()->user()->role == 'admin') {
                $book_service = BookService::query()
                    ->where('id', $book_id)
                    ->with('customer:id,mobile,country,city,photo')
                    ->with(
                        'service:id,service_name,photo',
                        'service.expert:id,service_id,mobile,country,city,photo,rating,price,working_hours'
                    )
                    ->get(['id', 'customer_id', 'service_id', 'description', 'delivery_time', 'status']);


                return response()->json([
                    'status' => 'success',
                    'data' => $book_service
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $book_id)
    {
        try {
            $book_service = BookService::query()->find($book_id);

            if (!$book_service) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Book service is not found',
                ], 403);
            }
            if (auth()->user()->role == 'expert') {
                $expert_id = BookService::query()
                    ->find($book_id)
                    ->service
                    ->expert
                    ->value('user_id');

                if ($expert_id != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $validate = Validator::make($request->all(), [
                    'status' => ['required'],
                    'delivery_time' => 'string'
                ]);

                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors(),
                    ], 403);
                }
                $book_service->update([
                    'status' => $request->status,
                    'delivery_time' => $request->delivery_time ?? $book_service->delivery_time,
                ]);
                return response()->json([
                    'status' => 'success',
                    'book_service' => $book_service,
                    'message' => 'Book service Updated Successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($book_id)
    {
        try {
            $book_service = BookService::query()->find($book_id);

            if (!$book_service) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Book service is not found',
                ], 403);
            }
            if (auth()->user()->role == 'expert') {
                $expert_id = BookService::query()
                    ->find($book_id)
                    ->service
                    ->expert
                    ->value('user_id');

                if ($expert_id != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $book_service->delete();
                return response()->json([
                    'status' => 'sucess',
                    'message' => 'Book Service Deleted Successfully'
                ], 200);
            }
            if (auth()->user()->role == 'customer') {
                $book_service = BookService::query()
                    ->where('id', $book_id)
                    ->where('customer_id', auth()->user()->id)
                    ->delete();
                if (!$book_service) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                return response()->json([
                    'status' => 'sucess',
                    'message' => 'Book Service Deleted Successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
