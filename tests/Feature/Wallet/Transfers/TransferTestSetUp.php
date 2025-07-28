<?php

namespace Tests\Feature\Wallet\Transfers;

use App\Models\User;
use App\Models\Wallet;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class TransferTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;

    protected User $sourceUser;
    protected User $targetUser;
    protected Wallet $targetWallet;
    protected string $targetPublicKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->sourceUser = UserFactory::new()->consumer()->create();
        $this->targetUser = UserFactory::new()->consumer()->create();
        $this->targetWallet = $this->targetUser->wallet;
        $this->targetPublicKey = $this->targetWallet->keys()->first()->public_key;

        $this->sourceUser->deposit('100');
    }

    protected function route(): string
    {
        return route('api.wallets.transfers.create');
    }
}
