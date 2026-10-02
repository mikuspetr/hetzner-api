<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_pages_require_an_authorized_administrator(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_create_view_update_and_delete_users(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        config(['users.admin_emails' => [$admin->email]]);
        $this->actingAs($admin);

        $this->get(route('users.index'))->assertOk();
        $this->get(route('users.create'))->assertOk();

        $this->post(route('users.store'), [
            'name' => 'Managed User',
            'email' => 'managed@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('users.show', User::where('email', 'managed@example.com')->first()));

        $user = User::where('email', 'managed@example.com')->firstOrFail();
        $this->assertTrue(password_verify('secret-password', $user->password));

        $this->get(route('users.show', $user))->assertOk();
        $this->get(route('users.edit', $user))->assertOk();

        $this->put(route('users.update', $user), [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('users.show', $user));

        $user->refresh();
        $this->assertSame('Updated User', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(password_verify('secret-password', $user->password));

        $this->delete(route('users.destroy', $user))->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_administrator_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        config(['users.admin_emails' => [$admin->email]]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_new_user_input_is_validated_and_cannot_set_privileged_fields(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com']);
        config(['users.admin_emails' => [$admin->email]]);

        $this->actingAs($admin)
            ->from(route('users.create'))
            ->post(route('users.store'), [
                'name' => 'Managed User',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'does-not-match',
                'hetzner_api_token' => 'untrusted-token',
            ])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }
}
