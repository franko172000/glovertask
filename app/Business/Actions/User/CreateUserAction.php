<?php

namespace App\Business\Actions\User;

use App\Business\Actions\Action;
use App\Persistence\Models\User;
use App\Traits\UserRulesTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class CreateUserAction extends Action
{
    use UserRulesTrait;
    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return $this->createRules();
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
        ]);

        return $user->createToken('apiToken')->plainTextToken;
    }
}
