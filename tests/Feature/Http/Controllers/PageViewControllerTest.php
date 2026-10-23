<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PageView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_visitor_is_counted_once_per_path_per_day(): void
    {
        $this->postJson('/api/track', ['path' => '/'])->assertNoContent();
        $this->postJson('/api/track', ['path' => '/'])->assertNoContent();
        $this->postJson('/api/track', ['path' => '/blog'])->assertNoContent();

        $this->assertDatabaseCount(PageView::class, 2);
    }

    public function test_missing_path_returns_422(): void
    {
        $this->postJson('/api/track', [])->assertJsonValidationErrors(['path' => 'Path wajib diisi.']);
    }
}
