<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_have_contact_details(): void
    {
        $user = User::factory()->create();

        $contact = $user->contact()->create([
            'phone' => '+420 123 456 789',
            'address' => 'Example Street 1',
            'city' => 'Prague',
            'postal_code' => '110 00',
            'country' => 'Czechia',
        ]);

        $this->assertSame($user->id, $contact->user->id);
        $this->assertSame($contact->id, $user->refresh()->contact->id);
        $this->assertDatabaseHas('contacts', [
            'user_id' => $user->id,
            'phone' => '+420 123 456 789',
            'address' => 'Example Street 1',
            'city' => 'Prague',
            'postal_code' => '110 00',
            'country' => 'Czechia',
        ]);
    }
}
