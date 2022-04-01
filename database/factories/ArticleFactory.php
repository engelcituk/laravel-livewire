<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'image' => $this->faker->imageUrl,
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
            'user_id' => User::factory(),
        ];
    }
}
