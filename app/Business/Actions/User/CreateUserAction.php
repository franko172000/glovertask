<?php

namespace App\Business\Actions\User;

use App\Business\Actions\Action;
use App\Enums\UsersEnum;
use App\Persistence\Models\User;
use App\Traits\UserRulesTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class CreateUserAction extends Action
{
    use UserRulesTrait;
    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return array_merge($this->createRules(), [
            'user_type' => 'string|in:'.implode(",", [UsersEnum::ADMIN->value, UsersEnum::CUSTOMER->value])
        ]);
    }

    /**
     * @return string|null
     * @throws AuthenticationException
     */
    public function execute(): string|null
    {
        $user = User::create([
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'phone' => $this->data['phone'],
            'email' => $this->data['email'],
            'password' => Hash::make($this->data['password']),
            'user_type' => Arr::get($this->data, 'user_type', UsersEnum::CUSTOMER->value)
        ]);

        return $user->createToken('apiToken')->plainTextToken;
    }
}
