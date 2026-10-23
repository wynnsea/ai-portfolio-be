<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_searches_and_filters_newest_first(): void
    {
        $this->actingAsAdmin();
        Message::factory()->create(['name' => 'Rina', 'created_at' => now()->subDay()]);
        Message::factory()->read()->create(['name' => 'Budi', 'created_at' => now()]);

        $this->getJson('/api/admin/messages')->assertJsonPath('data.*.name', ['Budi', 'Rina']);
        $this->getJson('/api/admin/messages?status=Baru')->assertJsonPath('data.*.name', ['Rina']);
        $this->getJson('/api/admin/messages?search=bud')->assertJsonPath('data.*.name', ['Budi']);
    }

    public function test_show_marks_new_message_as_read(): void
    {
        $this->actingAsAdmin();
        $message = Message::factory()->create();

        $this->getJson("/api/admin/messages/{$message->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'Dibaca')
            ->assertJsonPath('data.date', $message->created_at->toDateString());
    }

    public function test_update_changes_status(): void
    {
        $this->actingAsAdmin();
        $message = Message::factory()->create();

        $this->patchJson("/api/admin/messages/{$message->id}", ['status' => 'Dibalas'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Dibalas');
    }

    public function test_update_rejects_unknown_status_with_422(): void
    {
        $this->actingAsAdmin();
        $message = Message::factory()->create();

        $this->patchJson("/api/admin/messages/{$message->id}", ['status' => 'Spam'])
            ->assertJsonValidationErrors(['status' => 'Status yang dipilih tidak valid.']);
    }

    public function test_admin_cannot_create_messages(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/admin/messages', [])->assertStatus(405);
    }

    public function test_destroy_returns_204(): void
    {
        $this->actingAsAdmin();
        $message = Message::factory()->create();

        $this->deleteJson("/api/admin/messages/{$message->id}")->assertNoContent();
        $this->assertModelMissing($message);
    }
}
