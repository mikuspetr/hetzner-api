<?php

namespace Tests\Unit;

use App\Services\HetznerClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HetznerClientTest extends TestCase
{
    public function test_verify_token_returns_true_for_successful_response(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response(['servers' => []], 200),
        ]);

        $client = new HetznerClient;

        $this->assertTrue($client->verifyToken('a-valid-token'));
    }

    public function test_verify_token_returns_false_for_unauthorized_response(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => Http::response(['error' => ['message' => 'unauthorized']], 401),
        ]);

        $client = new HetznerClient;

        $this->assertFalse($client->verifyToken('an-invalid-token'));
    }
}
