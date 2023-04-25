<?php

namespace App\Http\Controllers;

use App\Business\Actions\User\CreateUserAction;
use App\Business\Actions\User\LoginUserAction;
use App\Enums\UsersEnum;
use App\Http\Requests\UserCreationRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function createUser(UserCreationRequest $request): \Illuminate\Http\JsonResponse
    {
        $token =  CreateUserAction::run(array_merge($request->validated(), [
            'user_type' => UsersEnum::ADMIN->value
        ]));
        return $this->respondSuccess('Admin user created!',201, [
            'access_token' => $token,
        ]);
    }
}
