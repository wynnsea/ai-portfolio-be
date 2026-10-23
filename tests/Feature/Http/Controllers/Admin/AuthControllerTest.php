<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_credentials_return_token_and_user(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia123']);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'rahasia123',
            'remember' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('user.id', $admin->id)
            ->assertJsonPath('user.email', 'admin@example.com')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
        $this->assertNull($admin->tokens()->first()->expires_at);
    }

    public function test_token_expires_after_one_day_without_remember(): void
    {
        $this->freezeTime();
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->postJson('/api/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'rahasia123',
            'remember' => false,
        ])->assertOk();

        $this->assertSame(
            now()->addDay()->toDateTimeString(),
            $admin->tokens()->first()->expires_at->toDateTimeString(),
        );
    }

    public function test_wrong_password_returns_422_with_indonesian_message(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia123']);

        $this->postJson('/api/admin/login', ['email' => 'admin@example.com', 'password' => 'salah'])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Email atau password salah.')
            ->assertJsonValidationErrors(['email' => 'Email atau password salah.']);
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/admin/login', ['email' => 'x@example.com', 'password' => 'salah'])->assertStatus(422);
        }

        $this->postJson('/api/admin/login', ['email' => 'x@example.com', 'password' => 'salah'])->assertStatus(429);
    }

    public function test_me_returns_401_without_token(): void
    {
        $this->getJson('/api/admin/me')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_me_returns_401_without_accept_header(): void
    {
        $this->get('/api/admin/me')->assertUnauthorized();
    }

    public function test_me_returns_the_authenticated_admin(): void
    {
        $admin = $this->actingAsAdmin();

        $this->getJson('/api/admin/me')
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id)
            ->assertJsonPath('data.email', $admin->email)
            ->assertJsonPath('data.photoUrl', null);
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $admin = User::factory()->create();
        $token = $admin->createToken('admin')->plainTextToken;

        $this->withToken($token)->postJson('/api/admin/logout')->assertNoContent();

        $this->assertSame(0, $admin->tokens()->count());
    }

    public function test_password_change_updates_password_and_revokes_other_tokens(): void
    {
        $admin = User::factory()->create(['password' => 'lama12345']);
        $admin->createToken('other-device');
        $token = $admin->createToken('admin')->plainTextToken;

        $this->withToken($token)->putJson('/api/admin/password', [
            'currentPassword' => 'lama12345',
            'password' => 'baru12345',
            'passwordConfirmation' => 'baru12345',
        ])->assertNoContent();

        $this->assertTrue(Hash::check('baru12345', $admin->refresh()->password));
        $this->assertSame(1, $admin->tokens()->count());
    }

    public function test_password_change_rejects_wrong_current_password_with_422(): void
    {
        $admin = User::factory()->create(['password' => 'lama12345']);
        $token = $admin->createToken('admin')->plainTextToken;

        $this->withToken($token)->putJson('/api/admin/password', [
            'currentPassword' => 'salah',
            'password' => 'baru12345',
            'passwordConfirmation' => 'beda12345',
        ])->assertJsonValidationErrors([
            'currentPassword' => 'Password saat ini salah.',
            'passwordConfirmation' => 'Konfirmasi password dan password harus sama.',
        ]);
    }
}
