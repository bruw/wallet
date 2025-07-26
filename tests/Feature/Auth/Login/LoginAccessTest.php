<?php

namespace Tests\Feature\Auth\Login;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;

class LoginAccessTest extends LoginTestSetUp
{
    public function test_a_user_should_be_able_to_login(): void
    {
        $this->postJson($this->route(), [
            'email' => $this->user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('flash_messages.login'))
                ->where('data.user.id', $this->user->id)
                ->where('data.user.name', $this->user->name)
                ->where('data.user.email', fn (string $email) => str($email)->is($this->user->email))
                ->where('data.user.cpf', fn (string $cpf) => str($cpf)->is($this->user->cpf))
                ->where('data.user.phone', fn (string $phone) => str($phone)->is($this->user->phone))
                ->has('data.token')
                ->missing('data.user.password')
            );
    }
}
