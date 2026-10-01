<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HetznerTest extends TestCase
{
    use RefreshDatabase;

    public function test_hetzner_connection_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/hetzner');

        $response->assertOk();
    }

    public function test_valid_token_can_be_connected(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response(['servers' => []], 200),
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/hetzner', [
                'hetzner_api_token' => 'a-valid-looking-token',
            ]);

        $response->assertRedirect('/hetzner');
        $response->assertSessionHas('status', 'hetzner-connected');

        $this->assertTrue($user->fresh()->hasHetznerApiToken());
        $this->assertSame('a-valid-looking-token', $user->fresh()->hetzner_api_token);
    }

    public function test_invalid_token_is_rejected(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response(['error' => ['message' => 'unauthorized']], 401),
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/hetzner', [
                'hetzner_api_token' => 'an-invalid-token',
            ]);

        $response->assertSessionHasErrorsIn('hetznerToken', ['hetzner_api_token']);
        $this->assertFalse($user->fresh()->hasHetznerApiToken());
    }

    public function test_token_can_be_disconnected(): void
    {
        $user = User::factory()->create([
            'hetzner_api_token' => 'some-token',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/hetzner');

        $response->assertRedirect('/hetzner');
        $this->assertFalse($user->fresh()->hasHetznerApiToken());
    }

    public function test_servers_are_listed_for_connected_account(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response([
                'servers' => [
                    ['name' => 'web-1', 'status' => 'running'],
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'hetzner_api_token' => 'some-token',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/hetzner/servers');

        $response->assertOk();
        $response->assertSee('web-1');
    }

    public function test_servers_page_shows_error_when_api_call_fails(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response(['error' => ['message' => 'unable to authenticate']], 403),
        ]);

        $user = User::factory()->create([
            'hetzner_api_token' => 'a-now-revoked-token',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/hetzner/servers');

        $response->assertOk();
        $response->assertSee('unable to authenticate');
    }

    public function test_servers_page_handles_missing_token(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/hetzner/servers');

        $response->assertOk();
    }
}
