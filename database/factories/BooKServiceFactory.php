<?php

namespace Database\Factories;

use App\Models\CustomerInfos;
use App\Models\Service;
use App\Models\User;
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
        return [
            'customer_id' => CustomerInfos::where('id' , $this->faker->unique()->numberBetween(1,5))->value('customer_id'),
            'service_id' => Service::where('id' , $this->faker->unique()->numberBetween(1,20))->first(),
            'description' => fake()->sentence,
            'delivery_time' => fake()->dateTimeBetween(now(), now()->addDays(10)),
        ];
    }
}
