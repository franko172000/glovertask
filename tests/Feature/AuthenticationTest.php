<?php

namespace Tests\Feature;

use App\Enums\UsersEnum;
use App\Persistence\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('test'),
            'user_type' => UsersEnum::ADMIN->value
        ]);
        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'test'
        ]);
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertArrayHasKey('access_token', $data);
        $this->assertNotNull($data['access_token']);
    }

    public function test_failed_login_attempt(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('test'),
            'user_type' => UsersEnum::ADMIN->value
        ]);
        $response = $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'testing'
        ]);
        $response->assertStatus(401);
    }

    public function test_login_validations(): void
    {
        User::factory()->create([
            'password' => Hash::make('test'),
            'user_type' => UsersEnum::ADMIN->value
        ]);
        $response = $this->postJson(route('login'), []);
        $response->assertStatus(422);
    }
}
