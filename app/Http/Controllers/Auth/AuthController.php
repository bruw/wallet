<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Auth\RegisterUserDto;
use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\UserLoginResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $loginDto = User::register(RegisterUserDto::fromRequest($request));

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.user', 1),
            ]))->merge(['data' => new UserLoginResource($loginDto)]),
            Response::HTTP_CREATED
        );
    }

    /**
     * Authenticate a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $loginDto = User::login($request->userByEmail(), $request->password());

        return response()->json(
            FlashMessage::success(trans('flash_messages.login'))
                ->merge(['data' => new UserLoginResource($loginDto)]),
            Response::HTTP_OK
        );
    }
}
