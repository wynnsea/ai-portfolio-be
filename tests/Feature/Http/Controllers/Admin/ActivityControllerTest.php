<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_payload_stores_photo_paths_and_returns_absolute_urls(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/activities', [
            'title' => 'Bunkasai',
            'role' => 'Fasilitator',
            'organizer' => 'Politeknik Takumi',
            'photos' => [Storage::disk('public')->url('activities/a.jpg'), 'activities/b.jpg'],
            'focus' => '50% 20%',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.photos', [
                Storage::disk('public')->url('activities/a.jpg'),
                Storage::disk('public')->url('activities/b.jpg'),
            ])
            ->assertJsonPath('data.focus', '50% 20%');
        $this->assertSame(['activities/a.jpg', 'activities/b.jpg'], Activity::query()->first()->photos);
    }

    public function test_missing_role_returns_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/activities', ['title' => 'Bunkasai'])
            ->assertJsonValidationErrors(['role' => 'Peran wajib diisi.']);
    }

    public function test_removing_a_photo_deletes_only_that_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('activities/a.jpg', 'a');
        Storage::disk('public')->put('activities/b.jpg', 'b');
        $activity = Activity::factory()->create(['photos' => ['activities/a.jpg', 'activities/b.jpg']]);

        $this->putJson("/api/admin/activities/{$activity->id}", ['photos' => ['activities/b.jpg']])->assertOk();

        Storage::disk('public')->assertMissing('activities/a.jpg');
        Storage::disk('public')->assertExists('activities/b.jpg');
    }

    public function test_index_searches_organizer(): void
    {
        $this->actingAsAdmin();
        Activity::factory()->create(['organizer' => 'PT Minori']);
        Activity::factory()->create(['organizer' => 'HIMA TI']);

        $this->getJson('/api/admin/activities?search=minori')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.organizer', 'PT Minori');
    }
}
