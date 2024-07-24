<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ExpertInfos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\URL;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category_count =  Category::all()->count();

        return [
            'category_id' => fake()->numberBetween(1, $category_count),
            'service_name' => fake()->unique()->word,
            'service_description' => fake()->sentence,
            'photo' => URL::to('/') . 'storage/services/' . time() . '.png',
        ];
    }
}
