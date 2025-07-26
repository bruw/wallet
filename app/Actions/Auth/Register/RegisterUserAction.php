<?php

namespace App\Actions\Auth\Register;

use App\Dto\Auth\RegisterUserDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserAction
{
    public function __construct(
        private RegisterUserDto $data
    ) {}

    public function execute(): User
    {
        try {
            return DB::transaction(function () {
                $user = $this->register();
                $this->syncRole($user);
                $this->logSuccess($user);

                return $user;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Register a new user based on the '$data' provided.
     */
    private function register(): User
    {
        return User::create([
            'name' => $this->data->name,
            'email' => $this->data->email,
            'cpf' => $this->data->cpf,
            'phone' => $this->data->phone,
            'password' => Hash::make($this->data->password),
        ]);
    }

    /**
     * Synchronizes the roles of the newly registered user with the consumer role.
     */
    private function syncRole(User $user): void
    {
        $user->roles()->attach(Role::consumer());
    }

    /**
     * Logs a success message when a new user is registered.
     */
    private function logSuccess(User $user): void
    {
        Log::info('User successfully registered.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Handles exceptions that occur during user registration.
     */
    private function handleException(Exception $e): void
    {
        Log::error('User registration failure.', [
            'cpf' => $this->data->cpf,
            'email' => $this->data->email,
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('actions.auth.errors.register'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
