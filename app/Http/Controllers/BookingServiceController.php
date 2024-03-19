<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\BookService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingServiceController extends Controller
{
    public function index()
    {
        try{
            if(auth()->user()->role == 'admin'){
                $book_service = BookService::query()
                ->with('customer:id,name','customer.customerInfos:customer_id,mobile,city')
                ->with('service:id,expert_id,photo','service.expert:id','service.expert.expertInfos:id,expert_id,mobile',)
                ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $book_service,
                ],200);
            }
            if(auth()->user()->role == 'customer'){
                $book_service = BookService::query()
                    ->where('customer_id',auth()->user()->id)->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $book_service,
                ],200);
            }

            if(auth()->user()->role == 'expert'){
                $service_id =  Service::query()
                    ->where('expert_id',auth()->user()->id)
                    ->get('id');
                $book_service = [];

                for($i = 0; $i < count($service_id); $i++){
                    $book_service_for_expert = BookService::query()
                    ->where('service_id',$service_id[$i]['id'])
                    ->get();
                    if($book_service_for_expert->count() != 0){
                        $book_service[$service_id[$i]['id']] = $book_service_for_expert;
                    }

                }
                return response()->json([
                    'status' => 'success',
                    'data' => $book_service
                ],200);
                // return $book_service;

            }

        }
        catch (\Exception $e) {
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
                    ->where('id',$book_id)
                    ->with('service:id,service_name,expert_id' , 'service.expert:id,name,email,role','service.expert.expertInfos')
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
                                    ->expert_id;
                if ($expert != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $book_service = BookService::query()
                    ->where('id',$book_id)
                    ->with('service','service.expert')
                    ->with('customer:id,name,email,role')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => $book_service
                ], 200);
            } else if (auth()->user()->role == 'admin')  {
                $book_service = BookService::query()->find($book_id)
                    ->with('service:id,service_name,expert_id', 'service.owner:id,name,mobile,role')
                    ->with('customer:id,name,mobile,role')
                    ->get();
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
            if (empty(BookService::find($book_id))) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Book service is not found',
                ]);
            }
            if (auth()->user()->role == 'expert') {
                $expert_id = BookService::query()
                                        ->find($book_id)
                                        ->service
                                        ->expert_id;

                if ($expert_id != auth()->user()->id) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Permission denied',
                    ], 403);
                }
                $validate = Validator::make($request->all(),[
                    'status' => ['required'],
                    'delivery_time' => 'string'
                ]);

                if($validate->fails()){
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors(),
                    ], 403);
                }
                // return [$request->status,BookService::query()->where('id',$book_id)->get('status')] ;
                $book_service = BookService::query()->find($book_id);
                $book_service->update([
                    'status' => $request->status,
                    'delivery_time' => $request->delivery_time,
                ]);
                // $book_service->status = $request->status;
                // $book_service->save();
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
    public function destroy(string $id)
    {
        //
    }
}
