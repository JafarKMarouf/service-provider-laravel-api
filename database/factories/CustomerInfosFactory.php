<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerInfos>
 */
class CustomerInfosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake()->unique()->userName();
        static $counter = 0;
        return [
            'user_id' => User::factory()->create([
                'name' => $faker,
                'email' => 'customer' . $counter++ . '@gmail.com',
                'password' => Hash::make('123456789'),
                'role' => 'customer'
            ])->getAttribute('id'),
            'mobile' => fake()->unique()->phoneNumber,
            'city' => fake()->city,
            'country' => fake()->country,
            'photo' => 'https://i.ibb.co/BTdNvBw/ellipse-12.jpg'

        ];
    }
}
