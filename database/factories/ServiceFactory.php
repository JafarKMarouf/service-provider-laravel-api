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
        $photos = [
            'https://i.ibb.co/Sn6PBHv/2ef3b2e1a7e8.png',
            'https://i.ibb.co/YpVTpR3/8816474b31d4.png',
            'https://i.ibb.co/2Pbj1Pd/92e857a0f66f.png',
            'https://i.ibb.co/TmXvDRZ/49dd9784bae7.png',
            'https://i.ibb.co/30jGQ8z/705b91af40d4.png',
            'https://i.ibb.co/hmyyH3x/5348849c13c0.png',

        ];
        return [
            'category_id' => fake()->numberBetween(1, $category_count),
            'service_name' => fake()->unique()->word,
            'service_description' => fake()->sentence,
            'photo' => $photos[rand(0, count($photos) - 1)],
        ];
    }
}
