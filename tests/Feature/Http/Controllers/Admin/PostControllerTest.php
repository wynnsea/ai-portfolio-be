<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishing_without_date_sets_today_and_estimates_read_time(): void
    {
        $this->freezeTime();
        $this->actingAsAdmin();

        $this->postJson('/api/admin/posts', [
            'title' => 'Cerita Bikin Sistem Rekrutmen',
            'category' => 'Integrasi AI',
            'status' => 'Terbit',
            'excerpt' => 'Ringkasan singkat.',
            'content' => [
                ['type' => 'p', 'text' => str_repeat('kata ', 450)],
                ['type' => 'list', 'items' => ['Satu', 'Dua']],
                ['type' => 'code', 'lang' => 'php', 'code' => 'echo 1;'],
            ],
        ])->assertCreated()
            ->assertJsonPath('data.slug', 'cerita-bikin-sistem-rekrutmen')
            ->assertJsonPath('data.date', today()->toDateString())
            ->assertJsonPath('data.readTime', '3 menit baca')
            ->assertJsonPath('data.content.1', ['type' => 'list', 'items' => ['Satu', 'Dua']]);
    }

    public function test_draft_keeps_date_empty(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/posts', ['title' => 'Draf', 'category' => 'Cerita', 'excerpt' => 'Ringkasan.'])
            ->assertCreated()
            ->assertJsonPath('data.status', 'Draft')
            ->assertJsonPath('data.date', null)
            ->assertJsonPath('data.readTime', '1 menit baca');
    }

    public function test_plain_text_content_is_split_into_paragraph_blocks(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/posts', [
            'title' => 'Teks Biasa',
            'category' => 'Cerita',
            'excerpt' => 'Ringkasan.',
            'content' => "Paragraf satu.\n\nParagraf dua\nmasih sama.",
        ])->assertCreated()
            ->assertJsonPath('data.content', [
                ['type' => 'p', 'text' => 'Paragraf satu.'],
                ['type' => 'p', 'text' => "Paragraf dua\nmasih sama."],
            ]);
    }

    public function test_invalid_content_blocks_return_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/posts', [
            'title' => 'X',
            'category' => 'Cerita',
            'excerpt' => 'Ringkasan.',
            'content' => [
                ['type' => 'video', 'src' => 'x'],
                ['type' => 'h2'],
                ['type' => 'list', 'items' => []],
            ],
        ])->assertJsonValidationErrors([
            'content.0' => 'Blok konten harus punya type p, h2, quote, list, atau code.',
            'content.1' => 'Blok konten bertipe h2 wajib punya text.',
            'content.2' => 'Blok konten bertipe list wajib punya items berupa daftar teks.',
        ]);
    }

    public function test_unknown_category_and_long_excerpt_return_422(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/posts', ['title' => 'X', 'category' => 'Gosip', 'excerpt' => str_repeat('a', 301)])
            ->assertJsonValidationErrors([
                'category' => 'Kategori yang dipilih tidak valid.',
                'excerpt' => 'Ringkasan maksimal 300 karakter.',
            ]);
    }

    public function test_explicit_read_time_is_kept(): void
    {
        $this->actingAsAdmin();
        $post = Post::factory()->create();

        $this->putJson("/api/admin/posts/{$post->id}", ['readTime' => '10 menit baca'])
            ->assertOk()
            ->assertJsonPath('data.readTime', '10 menit baca');
    }

    public function test_index_filters_by_status(): void
    {
        $this->actingAsAdmin();
        Post::factory()->published()->create();
        Post::factory()->create();

        $this->getJson('/api/admin/posts?status=Terbit')->assertJsonCount(1, 'data');
        $this->getJson('/api/admin/posts')->assertJsonCount(2, 'data');
    }
}
