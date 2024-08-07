<?php

namespace Database\Factories;

use App\Models\CustomerInfos;
use App\Models\ExpertInfos;
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

        $expert_count = ExpertInfos::all()->count();
        $expert = [];
        for ($i = 1; $i < $expert_count; $i++) {
            array_push($expert, $i);
        }

        static $count = 0;
        return [
            'customer_id' => $customer_id,
            'expert_id' => $expert[$count],
            'service_id' => ExpertInfos::where('id', $expert[$count])->value('service_id'),
            'description' => fake()->sentence,
            'delivery_date' => fake()->date('Y/m/d', 'now'),
            'delivery_time' => fake()->time(),
            'location' => fake()->address(),
            'id' => ++$count,
        ];
    }
}
