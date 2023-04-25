<?php

namespace App\Business\Actions\User;

use App\Business\Actions\Action;
use App\Persistence\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class LoginUserAction extends Action
{

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            'email' => 'string|email|required',
            'password' => 'string|required'
        ];
    }

    /**
     * @return string|null
     * @throws AuthenticationException
     */
    public function execute(): string|null
    {
        $user = User::where('email', $this->data['email'])->first();

        if($user && Hash::check($this->data['password'], $user->password)){
            return $user->createToken('apiToken')->plainTextToken;
        }
        throw new AuthenticationException();
    }
}
