<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\ExpertInfos;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'expert_id' => ExpertInfos::where('id' , $this->faker->numberBetween(1,3))->value('expert_id'),
            'category_id' => Category::where('id' , $this->faker->numberBetween(1,10))->first(),
            'service_name' => fake()->unique()->word,
            'service_description' => fake()->sentence,
            'photo' => fake()->imageUrl('640','640'),
            'price' => fake()->randomFloat(2,10,10000),
        ];
    }
}
