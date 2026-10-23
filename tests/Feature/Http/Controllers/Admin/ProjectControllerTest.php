<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_routes_return_401_without_token(): void
    {
        $this->getJson('/api/admin/projects')->assertUnauthorized();
        $this->postJson('/api/admin/projects')->assertUnauthorized();
    }

    public function test_valid_payload_creates_project_and_returns_201(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/projects', [
            'title' => 'Minori AI RCR',
            'category' => 'AI Grading · Rekrutmen',
            'year' => '2026',
            'role' => 'Full-stack & AI Integration',
            'status' => 'Terbit',
            'summary' => 'Website walk-in rekrutmen.',
            'stack' => 'Laravel, React, Integrasi LLM, FastAPI, MySQL',
            'titleJp' => '採用AI',
            'kanji' => '採',
            'tone' => 'dark',
            'coverUrl' => Storage::disk('public')->url('projects/cover.jpg'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'minori-ai-rcr')
            ->assertJsonPath('data.status', 'Terbit')
            ->assertJsonPath('data.stack', ['Laravel', 'React', 'Integrasi LLM', 'FastAPI', 'MySQL'])
            ->assertJsonPath('data.approach', [])
            ->assertJsonPath('data.links', [])
            ->assertJsonPath('data.titleJp', '採用AI')
            ->assertJsonPath('data.coverUrl', Storage::disk('public')->url('projects/cover.jpg'))
            ->assertJsonPath('data.sortOrder', 0);
        $this->assertDatabaseHas('projects', ['slug' => 'minori-ai-rcr', 'cover_path' => 'projects/cover.jpg']);
    }

    public function test_generated_slug_is_unique(): void
    {
        $this->actingAsAdmin();
        Project::factory()->create(['title' => 'Minori AI RCR', 'slug' => 'minori-ai-rcr']);

        $this->postJson('/api/admin/projects', [
            'title' => 'Minori AI RCR',
            'category' => 'AI',
            'year' => '2026',
            'summary' => 'Ringkasan.',
        ])->assertCreated()->assertJsonPath('data.slug', 'minori-ai-rcr-2')->assertJsonPath('data.status', 'Draft');
    }

    public function test_empty_payload_returns_422_with_indonesian_messages(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/projects', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'Judul wajib diisi.',
                'category' => 'Kategori wajib diisi.',
                'year' => 'Tahun wajib diisi.',
                'summary' => 'Ringkasan wajib diisi.',
            ]);
    }

    public function test_invalid_nested_values_return_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/projects', [
            'title' => 'Proyek',
            'category' => 'AI',
            'year' => '2026',
            'summary' => 'Ringkasan.',
            'status' => 'Publik',
            'tone' => 'pink',
            'results' => [['value' => '80%']],
            'links' => ['demo' => 'bukan-url'],
        ])->assertJsonValidationErrors([
            'status' => 'Status yang dipilih tidak valid.',
            'tone' => 'Warna cover yang dipilih tidak valid.',
            'results.0.label' => 'Label hasil wajib diisi.',
            'links.demo' => 'Link demo harus berupa URL yang valid.',
        ]);
    }

    public function test_index_searches_filters_and_paginates(): void
    {
        $this->actingAsAdmin();
        Project::factory()->published()->create(['title' => 'Minori AI', 'stack' => ['Laravel']]);
        Project::factory()->create(['title' => 'Chatbot', 'stack' => ['FastAPI', 'LangChain']]);
        Project::factory()->published()->count(7)->create(['stack' => ['Vue']]);

        $this->getJson('/api/admin/projects?search=langchain')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Chatbot');

        $this->getJson('/api/admin/projects?status=Draft')->assertJsonCount(1, 'data');

        $this->getJson('/api/admin/projects?status=Semua&page=2')
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.per_page', 6)
            ->assertJsonPath('meta.total', 9)
            ->assertJsonPath('meta.current_page', 2);
    }

    public function test_index_sorts_by_requested_column(): void
    {
        $this->actingAsAdmin();
        Project::factory()->create(['year' => '2024']);
        Project::factory()->create(['year' => '2026']);
        Project::factory()->create(['year' => '2025']);

        $this->getJson('/api/admin/projects?sort=-year')
            ->assertJsonPath('data.*.year', ['2026', '2025', '2024']);
    }

    public function test_update_accepts_partial_payload(): void
    {
        $this->actingAsAdmin();
        $project = Project::factory()->create(['title' => 'Lama', 'slug' => 'lama']);

        $this->putJson("/api/admin/projects/{$project->id}", ['title' => 'Baru'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Baru')
            ->assertJsonPath('data.slug', 'lama');
    }

    public function test_replacing_cover_deletes_old_file(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('projects/old.jpg', 'old');
        $project = Project::factory()->create(['cover_path' => 'projects/old.jpg']);

        $this->putJson("/api/admin/projects/{$project->id}", ['coverUrl' => 'projects/new.jpg'])->assertOk();

        Storage::disk('public')->assertMissing('projects/old.jpg');
    }

    public function test_destroy_deletes_project_and_cover_and_returns_204(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('projects/cover.jpg', 'img');
        $project = Project::factory()->create(['cover_path' => 'projects/cover.jpg']);

        $this->deleteJson("/api/admin/projects/{$project->id}")->assertNoContent();

        $this->assertModelMissing($project);
        Storage::disk('public')->assertMissing('projects/cover.jpg');
    }

    public function test_show_returns_404_for_unknown_id(): void
    {
        $this->actingAsAdmin();

        $this->getJson('/api/admin/projects/999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Data tidak ditemukan.');
    }

    public function test_reorder_sets_sort_order_from_id_positions(): void
    {
        $this->actingAsAdmin();
        [$a, $b, $c] = Project::factory()->count(3)->create();

        $this->patchJson('/api/admin/projects/reorder', ['ids' => [$c->id, $a->id, $b->id]])->assertNoContent();

        $this->assertSame([1, 2, 0], [$a->refresh()->sort_order, $b->refresh()->sort_order, $c->refresh()->sort_order]);
    }
}
