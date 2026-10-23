<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\CoverTone;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'category' => fake()->randomElement(['AI Grading · Rekrutmen', 'Chatbot', 'Web App']),
            'year' => (string) fake()->numberBetween(2023, 2026),
            'role' => 'Full-stack & AI Integration',
            'duration' => fake()->numberBetween(1, 6).' bulan',
            'status' => ContentStatus::Draft,
            'summary' => fake()->sentence(12),
            'overview' => fake()->paragraph(),
            'challenge' => fake()->paragraph(),
            'approach' => [fake()->sentence(), fake()->sentence()],
            'results' => [['value' => '80%', 'label' => 'Lebih cepat']],
            'stack' => ['Laravel', 'React', 'MySQL'],
            'links' => [],
            'title_jp' => '採用AI',
            'kanji' => '採',
            'tone' => fake()->randomElement(CoverTone::cases()),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ContentStatus::Published]);
    }
}
