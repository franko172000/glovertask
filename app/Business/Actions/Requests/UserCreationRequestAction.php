<?php

namespace App\Business\Actions\Requests;

use App\Business\Actions\Action;
use App\Enums\ActionRequestEnum;
use App\Jobs\NotifyAdminsJob;
use App\Persistence\Repositories\ActionRequestRepository;
use App\Traits\UserRulesTrait;

class UserCreationRequestAction extends Action
{
    use UserRulesTrait;

    public function rules(): array
    {
        return array_merge($this->createRules(), [
            'admin_id' => 'required|exists:users,id'
        ]);
    }

    public function execute()
    {
        $actionRequestRepo = app(ActionRequestRepository::class);
        $adminId = $this->data['admin_id'];
        //remove user id
        unset($this->data['admin_id']);
        $request = $actionRequestRepo->registerAction($adminId, $this->data, ActionRequestEnum::REQUEST_CREATE->value );

        NotifyAdminsJob::dispatch($adminId,ActionRequestEnum::REQUEST_CREATE->value);

        return $request;
    }
}
