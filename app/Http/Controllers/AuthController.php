<?php

namespace App\Http\Controllers;

use App\Business\Actions\User\LoginUserAction;
use App\Http\Requests\AuthRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(AuthRequest $request): \Illuminate\Http\JsonResponse
    {
        $token =  LoginUserAction::run([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);
        return $this->respondSuccess('Authenticated',200, [
            'access_token' => $token,
        ]);
    }
}
