<?php

namespace Tests\Feature;

use App\Enums\ActionRequestEnum;
use App\Enums\UsersEnum;
use App\Jobs\NotifyAdminsJob;
use App\Notifications\NotifyAdmins;
use App\Persistence\Models\ActionRequest;
use App\Persistence\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RequestsTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function test_unauthorized_user_creation_request(): void
    {
        $response = $this->postJson(route('user.create.request'), [
            'email' => 'test@test.com',
            'password' => 'test',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08024243374'
        ]);

        $response->assertStatus(401);
    }

    /**
     * A basic feature test example.
     */
    public function test_user_creation_request(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        Sanctum::actingAs($user);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'email' => 'test@test444.com',
            'password' => 'test',
        ];

        $response = $this->postJson(route('user.create.request'), $data);
        $response->assertStatus(201);

        Queue::assertPushed(NotifyAdminsJob::class, function ($job) use($user) {
            return $job->currentAdminId === $user->id && $job->requestType === ActionRequestEnum::REQUEST_CREATE->value;
        });

        $this->assertDatabaseHas(ActionRequest::class, [
            'user_id' => $user->id,
            'action_data' => json_encode($data),
            'status' => ActionRequestEnum::PENDING->value,
            'request_type' => ActionRequestEnum::REQUEST_CREATE->value,
        ]);
    }

    public function test_user_creation_request_validation(): void
    {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        Sanctum::actingAs($user);

        $data = [
            'first_name' => 'John',
            'email' => 'test@test444.com',
            'password' => 'test',
        ];

        $response = $this->postJson(route('user.create.request'), $data);
        $response->assertStatus(422);
    }

    public function test_user_update_request(): void {
        Queue::fake();
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        Sanctum::actingAs($user);

        $data = [
            'first_name' => 'Fred',
            'last_name' => 'James',
            'phone' => '1234567890',
        ];

        $response = $this->putJson(route('user.update.request', ['user' => $user2->id]), $data);
        $response->assertStatus(200);
        $data['user_id'] = $user2->id;

        Queue::assertPushed(NotifyAdminsJob::class, function ($job) use($user) {
            return $job->currentAdminId === $user->id && $job->requestType === ActionRequestEnum::REQUEST_UPDATE->value;
        });

        $this->assertDatabaseHas(ActionRequest::class, [
            'user_id' => $user->id,
            'action_data' => json_encode($data),
            'status' => ActionRequestEnum::PENDING->value,
            'request_type' => ActionRequestEnum::REQUEST_UPDATE->value,
        ]);
    }

    public function test_user_update_request_validation(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        Sanctum::actingAs($user);

        $data = [
            'first_name' => 123,
            'last_name' => []
        ];

        $response = $this->putJson(route('user.update.request', ['user' => $user2->id]), $data);
        $response->assertStatus(422);

    }

    public function test_throw_error_on_update_request_if_no_field_is_sent(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        Sanctum::actingAs($user);


        $response = $this->putJson(route('user.update.request', ['user' => $user2->id]), []);
        $response->assertStatus(400);
    }

    public function test_admin_can_update_own_profile(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        Sanctum::actingAs($user);

        $data = [
            'first_name' => 'Fred',
            'last_name' => 'James',
            'phone' => '1234567890',
        ];

        $response = $this->putJson(route('user.update.request', ['user' => $user->id]), $data);
        $response->assertStatus(200);
        $updatedUser = $user->refresh();
        $this->assertEquals($updatedUser->first_name, $data['first_name']);
        $this->assertEquals($updatedUser->last_name, $data['last_name']);
        $this->assertEquals($updatedUser->phone, $data['phone']);
    }

    public function test_user_delete_request(): void {
        Queue::fake();

        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('user.update.request', ['user' => $user2->id]));
        $response->assertStatus(200);

        Queue::assertPushed(NotifyAdminsJob::class, function ($job) use($user) {
            return $job->currentAdminId === $user->id && $job->requestType === ActionRequestEnum::REQUEST_DELETE->value;
        });

        $this->assertDatabaseHas(ActionRequest::class, [
            'user_id' => $user->id,
            'action_data' => json_encode(['user_id' => $user2->id]),
            'status' => ActionRequestEnum::PENDING->value,
            'request_type' => ActionRequestEnum::REQUEST_DELETE->value,
        ]);
    }

    public function test_request_creation_approval(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $data = [
            'first_name' => 'Fred',
            'last_name' => 'James',
            'phone' => '1234567890',
            'email' => 'test@james.com',
            'password' => 'test@james.com',
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_CREATE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.approve.request', ['request' => $request->id]));
        $response->assertStatus(200);

        unset($data['password']);
        $data['user_type'] = UsersEnum::CUSTOMER->value;
        $this->assertDatabaseHas(User::class, $data);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::APPROVED->value );
    }

    public function test_request_update_approval(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user3 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        $data = [
            'user_id' => $user3->id,
            'first_name' => 'Fred123',
            'last_name' => 'James123',
            'phone' => '1234567890',
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_UPDATE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.approve.request', ['request' => $request->id]));
        $response->assertStatus(200);

        unset($data['user_id']);
        $this->assertDatabaseHas(User::class, $data);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::APPROVED->value );
    }

    public function test_request_delete_approval(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user3 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        $data = [
            'user_id' => $user3->id
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_DELETE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.approve.request', ['request' => $request->id]));
        $response->assertStatus(200);

        $this->assertDatabaseMissing(User::class, [
            'id' => $user3->id
        ]);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::APPROVED->value );
    }

    public function test_request_delete_declined(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user3 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        $data = [
            'user_id' => $user3->id
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_DELETE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.decline.request', ['request' => $request->id]));
        $response->assertStatus(200);

        $this->assertDatabaseHas(User::class, [
            'id' => $user3->id
        ]);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::DECLINED->value );
    }

    public function test_request_update_declined(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user3 = User::factory()->create([
            'user_type' => UsersEnum::CUSTOMER->value
        ]);

        $data = [
            'user_id' => $user3->id,
            'first_name' => 'Fred123',
            'last_name' => 'James123',
            'phone' => '1234567890',
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_UPDATE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.decline.request', ['request' => $request->id]));
        $response->assertStatus(200);

        $this->assertDatabaseHas(User::class, [
            'first_name' => $user3->first_name,
            'last_name' => $user3->last_name,
            'phone' => $user3->phone,
        ]);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::DECLINED->value );
    }

    public function test_request_creation_declined(): void {
        $user = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $user2 = User::factory()->create([
            'user_type' => UsersEnum::ADMIN->value
        ]);

        $data = [
            'first_name' => 'Fred',
            'last_name' => 'James',
            'phone' => '1234567890',
            'email' => 'test@james.com',
            'password' => 'test@james.com',
        ];

        $request = ActionRequest::factory()->create([
            'user_id' => $user2->id,
            'action_data' => json_encode($data),
            'request_type' => ActionRequestEnum::REQUEST_CREATE->value,
            'status' => ActionRequestEnum::PENDING->value,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(route('user.decline.request', ['request' => $request->id]));
        $response->assertStatus(200);

        unset($data['password']);
        $this->assertDatabaseMissing(User::class, $data);
        $this->assertEquals($request->refresh()->status, ActionRequestEnum::DECLINED->value );
    }

}
