<?php

namespace App\Business\Actions\Requests;

use App\Business\Actions\Action;
use App\Business\Actions\User\UpdateUserAction;
use App\Enums\ActionRequestEnum;
use App\Persistence\Repositories\ActionRequestRepository;
use App\Traits\UserRulesTrait;

class UserUpdateRequestAction extends Action
{
    use UserRulesTrait;

    private array $validatedFields;

    public function rules(): array
    {
        return array_merge($this->updateRules(), [
            'admin_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $actionRequestRepo = app(ActionRequestRepository::class);

        $adminId = $this->validatedFields['admin_id'];
        $userId = $this->validatedFields['user_id'];

        //remove admin id
        unset($this->validatedFields['admin_id']);

        //allow admin update personal account
        if($userId === $adminId){
            return UpdateUserAction::run($this->validatedFields);
        }
        return $actionRequestRepo->registerAction($adminId, $this->validatedFields, ActionRequestEnum::REQUEST_UPDATE->value);
    }

    protected function validatedFields(array $fields): void
    {
        $this->validatedFields = $fields;
    }
}
