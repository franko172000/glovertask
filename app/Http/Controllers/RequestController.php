<?php

namespace App\Http\Controllers;

use App\Business\Actions\Requests\ApproveRequestAction;
use App\Business\Actions\Requests\DeclineRequestAction;
use App\Business\Actions\Requests\UserDeleteRequestAction;
use App\Business\Actions\Requests\UserUpdateRequestAction;
use App\Http\Requests\UserCreationRequest;
use App\Business\Actions\Requests\UserCreationRequestAction;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\RequestResource;
use App\Persistence\Models\ActionRequest;
use App\Persistence\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use ApiResponseTrait;

    private string $internalError = 'Oops! there appears to be an issue with your request. Please try again later.';

    /**
     * @param UserCreationRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function createUser(UserCreationRequest $request): JsonResponse
    {
        $actionRequest = UserCreationRequestAction::run(array_merge($request->validated(), [
            'admin_id' => $request->user()->id,
        ]));

        if($actionRequest){
            return $this->respondSuccess('User creation request submitted successfully!');
        }
        return $this->respondInternalError($this->internalError);
    }

    /**
     * @throws \Exception
     */
    public function updateUser(User $user, UserUpdateRequest $request): JsonResponse
    {
        $actionRequest = UserUpdateRequestAction::run(array_merge($request->validated(), [
            'admin_id' => request()->user()->id,
            'user_id' => $user->id,
        ]));

        if($actionRequest){
            return $this->respondSuccess('User update request submitted successfully!');
        }
        return $this->respondInternalError($this->internalError);
    }

    /**
     * @throws \Exception
     */
    public function deleteUser(User $user): JsonResponse
    {
        $actionRequest = UserDeleteRequestAction::run([
            'admin_id' => request()->user()->id,
            'user_id' => $user->id,
        ]);

        if($actionRequest){
            return $this->respondSuccess('User delete request submitted successfully!');
        }
        return $this->respondInternalError($this->internalError);
    }

    public function approve(ActionRequest $request): JsonResponse
    {
        $actionRequest = ApproveRequestAction::run([
            'actioned_by' => request()->user()->id,
            'request_id' => $request->id,
        ]);

        if($actionRequest){
            return $this->respondSuccess('Request was approved successfully!');
        }
        return $this->respondInternalError($this->internalError);
    }

    public function decline(ActionRequest $request): JsonResponse
    {
        $actionRequest = DeclineRequestAction::run([
            'actioned_by' => request()->user()->id,
            'request_id' => $request->id,
        ]);

        if($actionRequest){
            return $this->respondSuccess('Request was declined successfully!');
        }
        return $this->respondInternalError($this->internalError);
    }

    public function getRequests(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return RequestResource::collection(ActionRequest::pending()->get());
    }
}
