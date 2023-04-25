<?php

namespace App\Business\Actions\Requests;

use App\Business\Actions\Action;
use App\Business\Actions\User\UpdateUserAction;
use App\Enums\ActionRequestEnum;
use App\Exceptions\AccountDeleteException;
use App\Persistence\Repositories\ActionRequestRepository;
use App\Traits\UserRulesTrait;

class UserDeleteRequestAction extends Action
{
    private array $validatedFields;

    public function rules(): array
    {
        return [
            'admin_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    /**
     * @throws AccountDeleteException
     */
    public function execute()
    {
        $actionRequestRepo = app(ActionRequestRepository::class);

        $adminId = $this->data['admin_id'];
        $userId = $this->validatedFields['user_id'];
        //remove user id
        unset($this->data['admin_id']);

        if($userId === $adminId){
            throw new AccountDeleteException('Own account deletion is not allowed!');
        }
        return $actionRequestRepo->registerAction($adminId, $this->data, ActionRequestEnum::REQUEST_DELETE->value);
    }

    protected function validatedFields(array $fields): void
    {
        $this->validatedFields = $fields;
    }
}
