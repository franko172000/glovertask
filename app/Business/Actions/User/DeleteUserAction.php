<?php

namespace App\Business\Actions\User;

use App\Business\Actions\Action;
use App\Persistence\Models\User;
use App\Traits\UserRulesTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class DeleteUserAction extends Action
{

    private array $validatedFields;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id'
        ];
    }

    public function execute()
    {
        return User::where('id', $this->validatedFields['user_id'])->delete();
    }

    protected function validatedFields(array $fields): void
    {
        $this->validatedFields = $fields;
    }
}
