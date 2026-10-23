<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Certification;
use App\Models\Message;
use App\Models\PageView;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_counts_latest_items_and_completeness(): void
    {
        $this->actingAsAdmin();
        Project::factory()->published()->create(['title' => 'Minori AI RCR', 'year' => '2026', 'created_at' => now()->subDay()]);
        Certification::factory()->create(['title' => 'JLPT N4', 'year' => 'Des 2024', 'created_at' => now()]);
        Message::factory()->count(5)->create();
        Message::factory()->read()->create();

        $this->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('data.counts', [
                'projects' => 1,
                'certifications' => 1,
                'activities' => 0,
                'posts' => 0,
                'messages' => 6,
                'unreadMessages' => 5,
            ])
            ->assertJsonCount(4, 'data.latestMessages')
            ->assertJsonPath('data.latestContent', [
                ['type' => 'certification', 'title' => 'JLPT N4', 'year' => 'Des 2024'],
                ['type' => 'project', 'title' => 'Minori AI RCR', 'year' => '2026'],
            ])
            ->assertJsonPath('data.profileCompleteness.checks', [
                ['label' => 'Proyek', 'done' => true],
                ['label' => 'Sertifikasi', 'done' => true],
                ['label' => 'Kegiatan', 'done' => false],
                ['label' => 'CV', 'done' => false],
                ['label' => 'Artikel terbit', 'done' => false],
            ]);
    }

    public function test_weekly_visitors_counts_unique_visitors_per_day(): void
    {
        $this->travelTo(Carbon::parse('2026-10-21 10:00')); // Wednesday
        $this->actingAsAdmin();
        PageView::query()->create(['path' => '/', 'visited_on' => '2026-10-19', 'visitor_hash' => 'a']);
        PageView::query()->create(['path' => '/blog', 'visited_on' => '2026-10-19', 'visitor_hash' => 'a']);
        PageView::query()->create(['path' => '/', 'visited_on' => '2026-10-19', 'visitor_hash' => 'b']);
        PageView::query()->create(['path' => '/', 'visited_on' => '2026-10-21', 'visitor_hash' => 'c']);
        PageView::query()->create(['path' => '/', 'visited_on' => '2026-10-12', 'visitor_hash' => 'd']);

        $this->getJson('/api/admin/dashboard')
            ->assertJsonPath('data.weeklyVisitors', [
                ['day' => 'Sen', 'value' => 2, 'future' => false],
                ['day' => 'Sel', 'value' => 0, 'future' => false],
                ['day' => 'Rab', 'value' => 1, 'future' => false],
                ['day' => 'Kam', 'value' => 0, 'future' => true],
                ['day' => 'Jum', 'value' => 0, 'future' => true],
                ['day' => 'Sab', 'value' => 0, 'future' => true],
                ['day' => 'Min', 'value' => 0, 'future' => true],
            ]);
    }
}
