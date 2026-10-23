<?php

namespace Database\Factories;

use App\Enums\CertificationCategory;
use App\Enums\CoverTone;
use App\Models\Certification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true),
            'issuer' => fake()->company(),
            'category' => fake()->randomElement(CertificationCategory::cases()),
            'year' => (string) fake()->numberBetween(2023, 2026),
            'score' => (string) fake()->numberBetween(400, 600),
            'note' => fake()->sentence(),
            'credential_url' => null,
            'title_jp' => '英語能力',
            'kanji' => '英',
            'tone' => fake()->randomElement(CoverTone::cases()),
        ];
    }
}
