<?php

namespace Tests\Unit\Actions\Auth\Register;

use App\Dto\Auth\LoginDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserActionTest extends RegisterUserActionTestSetUp
{
    public function test_should_return_an_instance_of_login_dto_when_registration_is_successful(): void
    {
        $this->assertInstanceOf(LoginDto::class, User::register($this->data));
    }

    public function test_should_create_a_new_user_in_the_database(): void
    {
        $loginDto = User::register($this->data);

        $this->assertDatabaseHas('users', [
            'id' => $loginDto->user->id,
            'name' => $this->data->name,
            'email' => $this->data->email,
            'cpf' => $this->data->cpf,
            'phone' => $this->data->phone,
        ]);
    }

    public function test_should_create_a_wallet_for_the_new_user(): void
    {
        $user = (User::register($this->data))->user;
        $wallet = $user->wallet;

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_the_wallet_should_start_with_a_zero_balance(): void
    {
        $user = (User::register($this->data))->user;
        $wallet = $user->wallet;

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'balance' => 0,
        ]);
    }

    public function test_should_create_a_public_key_for_the_wallet(): void
    {
        $user = (User::register($this->data))->user;
        $wallet = $user->wallet;

        $this->assertDatabaseHas('wallet_keys', ['wallet_id' => $wallet->id]);
        $this->assertNotNull($wallet->keys()->first()->public_key);
    }

    public function test_should_throw_an_exception_when_an_internal_server_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.auth.errors.register'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        User::register($this->data);
    }
}
