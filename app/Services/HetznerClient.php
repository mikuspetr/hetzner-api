<?php

namespace App\Services;

use App\Exceptions\HetznerApiException;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * A thin client for the Hetzner Cloud API (https://docs.hetzner.cloud/).
 *
 * The client authenticates on behalf of a given user using the Hetzner
 * API token the user has stored on their account.
 */
class HetznerClient
{
    protected string $baseUrl;

    /**
     * Timeout (in seconds) for requests made to the Hetzner Cloud API.
     */
    protected int $timeout = 10;

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = rtrim($baseUrl ?? config('services.hetzner.base_url', 'https://api.hetzner.cloud/v1'), '/');
    }

    /**
     * Build an authenticated HTTP client for the given raw API token.
     */
    protected function requestWithToken(string $token): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($token)
            ->acceptJson()
            ->timeout($this->timeout);
    }

    /**
     * Build an authenticated HTTP client for the given user.
     */
    protected function requestFor(User $user): PendingRequest
    {
        if (! $user->hasHetznerApiToken()) {
            throw new HetznerApiException('This user has not connected a Hetzner Cloud API token yet.');
        }

        return $this->requestWithToken($user->hetzner_api_token);
    }

    /**
     * Verify that the given token is valid by calling a lightweight endpoint.
     *
     * @throws HetznerApiException if the Hetzner Cloud API could not be reached at all.
     */
    public function verifyToken(string $token): bool
    {
        try {
            $response = $this->requestWithToken($token)->get('/servers', ['per_page' => 1]);
        } catch (ConnectionException $exception) {
            Log::warning('Could not reach the Hetzner Cloud API while verifying a token.', [
                'message' => $exception->getMessage(),
            ]);

            throw new HetznerApiException('Could not reach the Hetzner Cloud API. Please try again later.');
        }

        return $response->successful();
    }

    /**
     * Retrieve the list of servers belonging to the user's Hetzner Cloud project.
     */
    public function servers(User $user): array
    {
        return $this->get($user, '/servers')['servers'] ?? [];
    }

    /**
     * Retrieve the authenticated project's data centers.
     */
    public function datacenters(User $user): array
    {
        return $this->get($user, '/datacenters')['datacenters'] ?? [];
    }

    /**
     * Perform a GET request against the Hetzner Cloud API on behalf of a user.
     */
    protected function get(User $user, string $uri, array $query = []): array
    {
        $response = $this->requestFor($user)->get($uri, $query);

        $this->throwIfFailed($response);

        return $response->json() ?? [];
    }

    protected function throwIfFailed(Response $response): void
    {
        if ($response->failed()) {
            $rawMessage = $response->json('error.message') ?? 'No additional details were provided.';

            Log::warning('Hetzner Cloud API request failed.', [
                'status' => $response->status(),
                'message' => $rawMessage,
            ]);

            throw new HetznerApiException(
                'The Hetzner Cloud API could not fulfil this request. Please try again later.',
                $response->status()
            );
        }
    }
}
