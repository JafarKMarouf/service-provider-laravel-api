<?php

namespace Database\Factories;

use App\Models\BookService;
use App\Models\ExpertInfos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
        $book_service_count = BookService::all()->count();
        $payments_expert_id = User::query()->where('role','expert')->value('id');
        $expert_count = User::query()->where('role','expert')->count();
        $book_service_expert = [];

        for ($i = 1; $i <= $book_service_count; $i++) {
			for ($j = 1; $j <= $expert_count; $j++) {
				array_push($book_service_expert, $i . "-" . $j + $payments_expert_id - 1);
			}
		}
        $payment = $this->faker->unique->randomElement($book_service_expert);

		$payment = explode('-', $payment);
        $book_service_id = $payment[0];
		$expert_id = $payment[1];


		return [
            'book_service_id' => $book_service_id,
            'payment_expert_id' => $expert_id,
			'operation_number' => $this->faker->numerify('6000########') ,
		];
	}
}
