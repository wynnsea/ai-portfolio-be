<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Certification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_payload_creates_certification_and_returns_201(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/certifications', [
            'title' => 'JLPT N4',
            'issuer' => 'Japan Foundation',
            'category' => 'Japanese',
            'year' => 'Des 2024',
            'credentialUrl' => 'https://example.com/verify',
            'kanji' => '語',
        ])->assertCreated()
            ->assertJsonPath('data.title', 'JLPT N4')
            ->assertJsonPath('data.category', 'Japanese')
            ->assertJsonPath('data.credentialUrl', 'https://example.com/verify')
            ->assertJsonPath('data.coverUrl', null)
            ->assertJsonPath('data.tone', 'dark');

        $this->assertDatabaseHas('certifications', ['title' => 'JLPT N4', 'category' => 'Japanese']);
    }

    public function test_unknown_category_returns_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/certifications', ['title' => 'X', 'issuer' => 'Y', 'category' => 'Math'])
            ->assertJsonValidationErrors(['category' => 'Kategori yang dipilih tidak valid.']);
    }

    public function test_kanji_longer_than_two_characters_returns_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/certifications', ['title' => 'X', 'issuer' => 'Y', 'category' => 'AI', 'kanji' => '日本語'])
            ->assertJsonValidationErrors(['kanji' => 'Kanji maksimal 2 karakter.']);
    }

    public function test_index_filters_by_category_and_searches_issuer(): void
    {
        $this->actingAsAdmin();
        Certification::factory()->create(['category' => 'Japanese', 'issuer' => 'Japan Foundation']);
        Certification::factory()->create(['category' => 'English', 'issuer' => 'ETS']);

        $this->getJson('/api/admin/certifications?category=English')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.issuer', 'ETS');

        $this->getJson('/api/admin/certifications?search=foundation')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'Japanese');
    }

    public function test_update_and_destroy(): void
    {
        $this->actingAsAdmin();
        $certification = Certification::factory()->create();

        $this->putJson("/api/admin/certifications/{$certification->id}", ['score' => '520'])
            ->assertOk()
            ->assertJsonPath('data.score', '520');

        $this->deleteJson("/api/admin/certifications/{$certification->id}")->assertNoContent();
        $this->assertModelMissing($certification);
    }
}
