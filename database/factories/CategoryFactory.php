<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\URL;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $photos = [
            'https://i.ibb.co/Sn6PBHv/2ef3b2e1a7e8.png',
            'https://i.ibb.co/YpVTpR3/8816474b31d4.png',
            'https://i.ibb.co/2Pbj1Pd/92e857a0f66f.png',
            'https://i.ibb.co/TmXvDRZ/49dd9784bae7.png',
            'https://i.ibb.co/30jGQ8z/705b91af40d4.png',
            'https://i.ibb.co/hmyyH3x/5348849c13c0.png',
        ];
        static $count = 0;
        return [
            'title' => fake()->unique()->word,
            'description' => fake()->sentence,
            'photo' => $photos[$count++],
            // 'photo' => $photos[rand(0, count($photos) - 1)],
            // 'photo' => URL::to('/') . '/storage/categories/' . time() . '.png',
        ];
    }
}
