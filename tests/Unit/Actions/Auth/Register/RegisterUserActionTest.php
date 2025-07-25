<?php

namespace Tests\Unit\Actions\Auth\Register;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserActionTest extends RegisterUserActionSetUpTest
{
    public function test_should_return_an_instance_of_the_user_when_registration_is_successful(): void
    {
        $this->assertInstanceOf(User::class, User::register($this->data));
    }

    public function test_should_create_a_new_user_in_the_database(): void
    {
        $user = User::register($this->data);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $this->data->name,
            'email' => $this->data->email,
            'cpf' => $this->data->cpf,
            'phone' => $this->data->cellPhone,
        ]);
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
