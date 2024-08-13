<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpertInfos>
 */
class ExpertInfosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $service_count =  Service::all()->count();
        static $counter = 0;

        return [
            'user_id' => User::factory()->create([
                'name' => fake()->unique()->userName(),
                'email' => 'expert' . $counter++ . '@gmail.com',
                'password' => Hash::make('123456789'),
                'role' => 'expert'
            ])->getAttribute('id'),
            'service_id' => fake()->unique()->numberBetween(1, $service_count),
            'mobile' => fake()->unique()->phoneNumber,
            'city' => fake()->city,
            'country' => fake()->country,
            'working_hours' => fake()->numberBetween(1, 23),
            'description' => fake()->sentence,
            'rating' => random_int(1, 5),
            'price' => fake()->numberBetween(1000, 150000),
            'photo' => 'https://i.ibb.co/Fnw45yT/ellipse-121.jpg',

        ];
    }
}
