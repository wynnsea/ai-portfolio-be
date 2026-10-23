<?php

namespace Database\Factories;

use App\Enums\CoverTone;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
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
            'role' => fake()->randomElement(['Peserta', 'Panitia', 'Fasilitator']),
            'organizer' => 'Politeknik Takumi',
            'category' => fake()->randomElement(['Program', 'Social', 'Community']),
            'year' => (string) fake()->numberBetween(2023, 2026),
            'description' => fake()->paragraph(),
            'photos' => [],
            'focus' => null,
            'title_jp' => '文化祭',
            'kanji' => '祭',
            'tone' => fake()->randomElement(CoverTone::cases()),
        ];
    }
}
