<?php

namespace App\Http\Controllers;

use App\Models\BookService;
use App\Models\CustomerInfos;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        try {
            if (auth()->user()->role == 'expert') {
                $expert_payments =  Payment::query()
                    ->where('payment_expert_id', auth()->user()->id)
                    ->with(
                        'bookservice:id,customer_id,service_id',
                        'bookservice.service:id,service_name',
                        'bookservice.service.expert:id,service_id,mobile,country,city,price',
                        'bookservice.customer:id,user_id,mobile,country,city,photo',
                        'bookservice.customer.user:id,name,email',
                    )
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'payment_expert_id', 'book_service_id', 'operation_number', 'created_at']);
                if (count($expert_payments) > 0) {
                    return response()->json([
                        'status' => 'success',
                        'count' => count($expert_payments),
                        'data' => $expert_payments
                    ], 200);
                }

                return response()->json([
                    'status' => 'failed',
                    'message' => 'Not Found any payments yet for ' . auth()->user()->name,
                ], 404);
            } else if (auth()->user()->role = 'customer') {
                $user_id = auth()->user()->id;

                $customer_id = CustomerInfos::where('user_id', $user_id)->value('id');
                // return $customer_id;
                $book_service_id = BookService::query()
                    ->where('customer_id', $customer_id)
                    ->get('id');

                // return $book_service_id;
                // $book_service_id = BookService::query()
                //     ->where('customer_id', auth()->user()->id)
                //     ->get('id');
                $payments = [];
                for ($i = 0; $i < count($book_service_id); $i++) {
                    $payments_for_customer = Payment::query()
                        ->where('book_service_id', $book_service_id[$i]['id'],)
                        ->with('bookservice')
                        ->get();
                    if ($payments_for_customer->count() != 0) {
                        $payments[$book_service_id[$i]['id']] = $payments_for_customer;
                    }
                }
                if (count($payments)) {
                    return response()->json([
                        'status' => 'success',
                        'count' => count($payments),
                        'data' => $payments,
                    ], 200);
                }
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Not Found any payments yet for ' . auth()->user()->name,
                ], 404);
            } else {
                return response()->json([], 401);
            }
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
            if (auth()->user()->role != 'customer') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied',
                ], 401);
            }
            $customer_book_service = BookService::query()
                ->where('id', $request->book_service_id)
                ->value('customer_id');

            $expert_book_service = BookService::query()
                ->where('id', $request->book_service_id)
                ->value('expert_id');

            $user_id = CustomerInfos::query()
                ->where('id', $customer_book_service)
                ->value('user_id');

            if (auth()->user()->id != $user_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Access denied',
                ], 401);
            }
            $validate = Validator::make($request->all(), [
                'book_service_id' => ['exists:book_services,id', 'required'],
                'operation_number' => ['string', 'required', 'unique:payments,operation_number', 'max:12'],
                'amount' => ['string', 'required']
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $validate->errors(),
                ], 403);
            }
            $payment =  Payment::create([
                'book_service_id' => $request->book_service_id,
                'payment_expert_id' => $expert_book_service,
                'amount' => $request->amount,
                'operation_number' => $request->operation_number,
            ]);

            return response()->json([
                'status' => 'sucess',
                'data' => $payment,
                'message' => 'Payment Created Successfully! '
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
