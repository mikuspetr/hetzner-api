<?php

namespace App\Http\Controllers;

use App\Exceptions\HetznerApiException;
use App\Http\Requests\UpdateHetznerTokenRequest;
use App\Services\HetznerClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class HetznerController extends Controller
{
    public function __construct(protected HetznerClient $hetzner) {}

    /**
     * Show the page used to connect the authenticated user's Hetzner Cloud account.
     */
    public function edit(Request $request): View
    {
        return view('hetzner.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Store (or update) the Hetzner API token for the authenticated user.
     */
    public function update(UpdateHetznerTokenRequest $request): RedirectResponse
    {
        $token = $request->validated('hetzner_api_token');

        if (! $this->hetzner->verifyToken($token)) {
            return Redirect::route('hetzner.edit')
                ->withErrors(['hetzner_api_token' => __('Hetzner rejected this API token. Please check that you copied it correctly.')], 'hetznerToken');
        }

        $request->user()->update([
            'hetzner_api_token' => $token,
        ]);

        return Redirect::route('hetzner.edit')->with('status', 'hetzner-connected');
    }

    /**
     * Remove the stored Hetzner API token for the authenticated user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->update([
            'hetzner_api_token' => null,
        ]);

        return Redirect::route('hetzner.edit')->with('status', 'hetzner-disconnected');
    }

    /**
     * Display the servers belonging to the user's connected Hetzner Cloud project.
     */
    public function servers(Request $request): View
    {
        $user = $request->user();
        $servers = [];
        $error = null;

        if ($user->hasHetznerApiToken()) {
            try {
                $servers = $this->hetzner->servers($user);
            } catch (HetznerApiException $exception) {
                $error = $exception->getMessage();
            }
        }

        return view('hetzner.servers', [
            'servers' => $servers,
            'error' => $error,
        ]);
    }
}
