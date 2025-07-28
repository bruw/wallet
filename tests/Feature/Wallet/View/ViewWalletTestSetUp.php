<?php

namespace Tests\Feature\Wallet\View;

use App\Models\User;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class ViewWalletTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->user = UserFactory::new()->consumer()->create();
    }

    protected function route(): string
    {
        return route('api.wallets.view');
    }
}
