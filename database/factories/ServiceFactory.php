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
        $categorys = [];
        for ($i = 1; $i < $category_count; $i++) {
            array_push($categorys, $i);
        }
        $category_id = $this->faker->unique->randomElement($categorys);


        return [
            'category_id' => $category_id,
            'service_name' => fake()->unique()->word,
            'service_description' => fake()->sentence,
            'photo' => URL::to('/') . 'storage/services/' . time() . '.png',
        ];
    }
}
