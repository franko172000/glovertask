<?php

namespace App\Persistence\Repositories;

use App\Enums\ActionRequestEnum;
use App\Persistence\Models\ActionRequest;

class ActionRequestRepository extends BaseRepository
{
    public function __construct(ActionRequest $model)
    {
        $this->model = $model;
    }

    public function registerAction(string $adminId, array $data, string $type)
    {
        return $this->model->create([
            'user_id' => $adminId,
            'action_data' => json_encode($data),
            'request_type' => $type
        ]);
    }

    public function getRequest(int $requestId){
        return $this->model->where('id', $requestId)->first();
    }

    public function approveRequest(int $requestId, int $adminId){
        return $this->model->where('id', $requestId)->update([
            'status' => ActionRequestEnum::APPROVED->value,
            'actioned_by' => $adminId
        ]);
    }

    public function declineRequest(int $requestId, int $adminId){
        return $this->model->where('id', $requestId)->update([
            'status' => ActionRequestEnum::DECLINED->value,
            'actioned_by' => $adminId
        ]);
    }

}
