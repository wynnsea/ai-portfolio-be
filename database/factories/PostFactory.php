<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\CoverTone;
use App\Enums\PostCategory;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'category' => fake()->randomElement(PostCategory::cases()),
            'status' => ContentStatus::Draft,
            'excerpt' => fake()->sentence(15),
            'content' => [
                ['type' => 'p', 'text' => fake()->paragraph()],
                ['type' => 'h2', 'text' => fake()->sentence(3)],
                ['type' => 'list', 'items' => [fake()->sentence(), fake()->sentence()]],
            ],
            'title_jp' => '採用AI',
            'kanji' => '採',
            'tone' => fake()->randomElement(CoverTone::cases()),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Published,
            'date' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
        ]);
    }
}
