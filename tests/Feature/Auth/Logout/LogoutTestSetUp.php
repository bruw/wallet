<?php

namespace Tests\Feature\Auth\Logout;

use App\Models\User;
use Database\Factories\UserFactory;
use Tests\TestCase;

class LogoutTestSetUp extends TestCase
{
    protected User $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->token = $this->user->createToken('auth-token');
    }
}
