<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_merges_partial_nested_values(): void
    {
        $this->actingAsAdmin();
        Setting::query()->create([
            'name' => 'Wayan',
            'availability' => ['open' => true, 'label' => 'Siap bekerja', 'detail' => 'Full-time'],
            'features' => ['chatbot' => true, 'preloader' => true],
        ]);

        $this->putJson('/api/admin/settings', [
            'role' => 'AI Engineer',
            'availability' => ['open' => false],
            'features' => ['preloader' => false],
            'socials' => [['label' => 'GitHub', 'href' => 'https://github.com/wynnsea']],
        ])->assertOk()
            ->assertJsonPath('data.name', 'Wayan')
            ->assertJsonPath('data.role', 'AI Engineer')
            ->assertJsonPath('data.availability', ['open' => false, 'label' => 'Siap bekerja', 'detail' => 'Full-time'])
            ->assertJsonPath('data.features', ['chatbot' => true, 'preloader' => false])
            ->assertJsonPath('data.socials.0.href', 'https://github.com/wynnsea');

        $this->assertSame(1, Setting::query()->count());
    }

    public function test_replacing_photo_deletes_old_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('profile/old.jpg', 'old');
        Setting::query()->create(['photo_path' => 'profile/old.jpg']);

        $this->putJson('/api/admin/settings', ['photoUrl' => Storage::disk('public')->url('profile/new.jpg')])
            ->assertOk()
            ->assertJsonPath('data.photoUrl', Storage::disk('public')->url('profile/new.jpg'));

        Storage::disk('public')->assertMissing('profile/old.jpg');
    }

    public function test_invalid_values_return_422(): void
    {
        $this->actingAsAdmin();

        $this->putJson('/api/admin/settings', [
            'email' => 'bukan-email',
            'socials' => [['label' => 'GitHub', 'href' => 'github']],
        ])->assertJsonValidationErrors([
            'email' => 'Email harus berupa alamat email yang valid.',
            'socials.0.href' => 'Link media sosial harus berupa URL yang valid.',
        ]);
    }
}
