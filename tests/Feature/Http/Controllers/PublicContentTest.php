<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Activity;
use App\Models\Certification;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Read-only endpoints used by the website.
 */
class PublicContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_lists_only_published_in_sort_order_without_pagination(): void
    {
        Project::factory()->published()->create(['title' => 'Kedua', 'sort_order' => 1]);
        Project::factory()->published()->create(['title' => 'Pertama', 'sort_order' => 0]);
        Project::factory()->create(['title' => 'Draf']);
        Project::factory()->create(['title' => 'Arsip', 'status' => 'Arsip']);

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonPath('data.*.title', ['Pertama', 'Kedua'])
            ->assertJsonMissingPath('meta');
    }

    public function test_project_detail_by_slug_hides_drafts_with_404(): void
    {
        Project::factory()->published()->create(['slug' => 'minori-ai-rcr']);
        Project::factory()->create(['slug' => 'rahasia']);

        $this->getJson('/api/projects/minori-ai-rcr')->assertOk()->assertJsonPath('data.slug', 'minori-ai-rcr');
        $this->getJson('/api/projects/rahasia')->assertNotFound()->assertJsonPath('message', 'Data tidak ditemukan.');
    }

    public function test_posts_list_is_published_newest_first_without_content(): void
    {
        Post::factory()->published()->create(['title' => 'Lama', 'date' => '2026-01-01']);
        Post::factory()->published()->create(['title' => 'Baru', 'date' => '2026-10-01']);
        Post::factory()->create(['title' => 'Draf']);

        $this->getJson('/api/posts')
            ->assertOk()
            ->assertJsonPath('data.*.title', ['Baru', 'Lama'])
            ->assertJsonMissingPath('data.0.content')
            ->assertJsonPath('data.0.date', '2026-10-01');
    }

    public function test_post_detail_includes_content(): void
    {
        $post = Post::factory()->published()->create();

        $this->getJson("/api/posts/{$post->slug}")
            ->assertOk()
            ->assertJsonPath('data.content', $post->content);
    }

    public function test_certifications_and_activities_list_all_in_sort_order(): void
    {
        Storage::fake('public');
        Certification::factory()->create(['title' => 'B', 'sort_order' => 1, 'cover_path' => 'certifications/b.jpg']);
        Certification::factory()->create(['title' => 'A', 'sort_order' => 0]);
        Activity::factory()->create(['title' => 'Bunkasai', 'photos' => ['activities/bunkasai.jpg']]);

        $this->getJson('/api/certifications')
            ->assertJsonPath('data.*.title', ['A', 'B'])
            ->assertJsonPath('data.1.coverUrl', Storage::disk('public')->url('certifications/b.jpg'));
        $this->getJson('/api/activities')
            ->assertJsonPath('data.0.photos', [Storage::disk('public')->url('activities/bunkasai.jpg')]);
    }

    public function test_settings_returns_profile_with_defaults(): void
    {
        Setting::query()->create(['brand' => 'Wynnsea', 'name' => 'Wayan', 'cv_path' => null]);

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('data.brand', 'Wynnsea')
            ->assertJsonPath('data.cvUrl', null)
            ->assertJsonPath('data.socials', [])
            ->assertJsonPath('data.features', ['chatbot' => true, 'preloader' => true]);
    }

    public function test_unknown_endpoint_returns_json_404(): void
    {
        $this->getJson('/api/tidak-ada')->assertNotFound()->assertJsonPath('message', 'Endpoint tidak ditemukan.');
    }

    public function test_cors_allows_frontend_origin_with_authorization_header(): void
    {
        $this->withHeaders([
            'Origin' => 'http://localhost:3000',
            'Access-Control-Request-Method' => 'GET',
            'Access-Control-Request-Headers' => 'authorization',
        ])->options('/api/admin/me')
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000');
    }
}
