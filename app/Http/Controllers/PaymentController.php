<?php

namespace App\Http\Controllers;

use App\Models\BookService;
use App\Models\ExpertInfos;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		try {
			if (auth()->user()->role == 'expert') {
				$expert_payments =  Payment::query()
					->where('payment_expert_id', auth()->user()->id)
					->with('bookservice:id,customer_id', 'bookservice.customer:id,name,email', 'bookservice.customer.customerInfos:customer_id,mobile,country,city,photo')
					->get(['id', 'payment_expert_id', 'book_service_id', 'operation_number', 'created_at']);
				//return count($expert_payments);
				if (count($expert_payments) > 0) {
					return response()->json([
						'status' => 'success',
						'data' => $expert_payments
					], 200);
				}
				return response()->json([
					'status' => 'failed',
					'message' => 'Not Found any payments yet for ' . auth()->user()->name,
				], 404);
			} else if (auth()->user()->role = 'customer') {
				$book_service_id = BookService::query()
					->where('customer_id', auth()->user()->id)
					->get('id');
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

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		//
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
