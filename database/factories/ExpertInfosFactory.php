<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

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
		$faker = fake()->unique()->userName();
		static $counter = 0;
		return [
			'expert_id' => User::factory()->create([
				'name' => $faker,
				'email' => 'expert' . $counter++ . '@gmail.com',
				'password' => Hash::make('123456789'),
				'role' => 'expert'
			])->getAttribute('id'),
			'mobile' => fake()->unique()->phoneNumber,
			'city' => fake()->city,
			'country' => fake()->country,
			'working_hours' => fake()->time,
			'description' => fake()->sentence,
			'certificate' => fake()->sentence,
            'rating' => random_int(0,5),
			'photo' => URL::to('/') . '/storage/experts/' . time() . '.png',
		];
	}
}
