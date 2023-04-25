<?php

namespace App\Business\Actions\User;

use App\Business\Actions\Action;
use App\Persistence\Models\User;
use App\Traits\UserRulesTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction extends Action
{
    use UserRulesTrait;

    private array $validatedFields;

    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return $this->updateRules();
    }

    public function execute()
    {
        return User::where('id', $this->data['user_id'])->update([
            'first_name' => $this->validatedFields['first_name'],
            'last_name' => $this->validatedFields['last_name'],
            'phone' => $this->validatedFields['phone'] ?? null,
        ]);
    }

    protected function validatedFields(array $fields): void
    {
        $this->validatedFields = $fields;
    }
}
