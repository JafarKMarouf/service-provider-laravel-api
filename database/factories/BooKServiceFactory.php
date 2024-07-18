<?php

namespace Database\Factories;

use App\Models\CustomerInfos;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookService>
 */
class BooKServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer_count = CustomerInfos::all()->count();
        $service_count = Service::all()->count();

        $customer_services = [];

        for ($i = 1; $i <= $customer_count; $i++) {
            for ($j = 1; $j <= $service_count; $j++) {
                array_push($customer_services, $i . "-" . $j);
            }
        }

        $customer_and_service = $this->faker->unique->randomElement($customer_services);
        $customer_and_service = explode('-', $customer_and_service);
        $customer_id = $customer_and_service[0];
        $service_id = $customer_and_service[1];

        return [
            'customer_id' => $customer_id,
            'service_id' => $service_id,
            'description' => fake()->sentence,
            'delivery_time' => fake()->dateTimeBetween(
                now(),
                now()->addDays(10)
            ),
        ];
    }
}
