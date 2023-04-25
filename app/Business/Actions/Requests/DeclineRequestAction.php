<?php

namespace App\Business\Actions\Requests;

use App\Business\Actions\Action;
use App\Business\Actions\User\CreateUserAction;
use App\Business\Actions\User\DeleteUserAction;
use App\Business\Actions\User\UpdateUserAction;
use App\Enums\ActionRequestEnum;
use App\Persistence\Repositories\ActionRequestRepository;
use App\Traits\UserRulesTrait;

class DeclineRequestAction extends Action
{
    use UserRulesTrait;

    public function rules(): array
    {
        return [
            'actioned_by' => 'required|exists:users,id',
            'request_id' => 'required|exists:action_requests,id'
        ];
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        $actionRequestRepo = app(ActionRequestRepository::class);
        $adminId = $this->data['actioned_by'];

        return $actionRequestRepo->declineRequest($this->data['request_id'], $adminId);
    }
}
