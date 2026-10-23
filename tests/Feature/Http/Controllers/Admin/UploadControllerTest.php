<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_upload_returns_201_with_url_and_path(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/uploads', [
            'folder' => 'projects',
            'file' => UploadedFile::fake()->image('cover.png'),
        ]);

        $response->assertCreated();
        $path = $response->json('path');
        $this->assertStringStartsWith('projects/', $path);
        $response->assertJsonPath('url', Storage::disk('public')->url($path));
        Storage::disk('public')->assertExists($path);
    }

    public function test_cv_accepts_pdf(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->postJson('/api/admin/uploads', [
            'folder' => 'cv',
            'file' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertCreated();
    }

    public function test_wrong_file_type_for_folder_returns_422(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->postJson('/api/admin/uploads', [
            'folder' => 'projects',
            'file' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertJsonValidationErrors(['file' => 'File harus berupa file bertipe: jpg, jpeg, png, webp.']);
    }

    public function test_oversized_image_returns_422(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->postJson('/api/admin/uploads', [
            'folder' => 'profile',
            'file' => UploadedFile::fake()->image('big.jpg')->size(5000),
        ])->assertJsonValidationErrors(['file' => 'File maksimal 4096 kilobyte.']);
    }

    public function test_unknown_folder_returns_422(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $this->postJson('/api/admin/uploads', [
            'folder' => '../private',
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])->assertJsonValidationErrors(['folder' => 'Folder yang dipilih tidak valid.']);
    }
}
