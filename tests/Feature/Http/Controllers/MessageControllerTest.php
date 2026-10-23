<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Message;
use App\Notifications\NewMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<string, string>
     */
    private array $payload = [
        'name' => 'Rina',
        'email' => 'rina@example.com',
        'subject' => 'Tawaran posisi',
        'message' => 'Halo Wayan, ada lowongan AI engineer.',
    ];

    public function test_valid_message_is_stored_and_admin_is_notified(): void
    {
        Notification::fake();
        config(['portfolio.admin.email' => 'admin@example.com']);

        $this->postJson('/api/messages', $this->payload)->assertCreated();

        $this->assertDatabaseHas('messages', ['email' => 'rina@example.com', 'status' => 'Baru']);
        Notification::assertSentTo(
            new AnonymousNotifiable,
            NewMessageReceived::class,
            fn ($notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'admin@example.com',
        );
    }

    public function test_filled_honeypot_returns_201_without_storing(): void
    {
        Notification::fake();

        $this->postJson('/api/messages', [...$this->payload, 'website' => 'http://spam.example'])->assertCreated();

        $this->assertDatabaseCount(Message::class, 0);
        Notification::assertNothingSent();
    }

    public function test_invalid_message_returns_422_with_indonesian_messages(): void
    {
        $this->postJson('/api/messages', ['email' => 'bukan-email', 'message' => str_repeat('a', 2001)])
            ->assertJsonValidationErrors([
                'name' => 'Nama wajib diisi.',
                'email' => 'Email harus berupa alamat email yang valid.',
                'subject' => 'Subjek wajib diisi.',
                'message' => 'Pesan maksimal 2000 karakter.',
            ]);
    }

    public function test_more_than_three_messages_per_minute_returns_429(): void
    {
        Notification::fake();

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/messages', $this->payload)->assertCreated();
        }

        $this->postJson('/api/messages', $this->payload)->assertStatus(429);
        $this->assertDatabaseCount(Message::class, 3);
    }
}
