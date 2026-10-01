<?php

namespace Tests\Unit;

use App\Exceptions\HetznerApiException;
use App\Services\HetznerClient;
use Illuminate\Http\Client\ConnectionException;
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

    public function test_verify_token_throws_when_api_is_unreachable(): void
    {
        Http::fake([
            'api.hetzner.cloud/*' => fn () => throw new ConnectionException('Connection timed out.'),
        ]);

        $client = new HetznerClient;

        $this->expectException(HetznerApiException::class);

        $client->verifyToken('some-token');
    }
}
