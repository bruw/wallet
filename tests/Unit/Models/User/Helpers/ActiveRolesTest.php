<?php

namespace Tests\Unit\Models\User\Helpers;

use App\Models\Role;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveRolesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = UserFactory::new()->create();
    }

    public function test_should_return_true_when_the_user_has_role_admin(): void
    {
        $this->user->roles()->attach(Role::admin());

        $this->assertTrue($this->user->isAdmin());
    }

    public function test_should_return_false_when_the_user_does_not_have_role_admin(): void
    {
        $this->assertFalse($this->user->isAdmin());
    }

    public function test_should_return_true_when_user_has_role_consumer(): void
    {
        $this->user->roles()->attach(Role::consumer());

        $this->assertTrue($this->user->isConsumer());
    }

    public function test_should_return_false_when_user_does_not_have_role_consumer(): void
    {
        $this->assertFalse($this->user->isConsumer());
    }
}
