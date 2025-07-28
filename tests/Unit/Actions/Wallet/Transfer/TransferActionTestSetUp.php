<?php

namespace Tests\Unit\Actions\Wallet\Transfer;

use App\Models\User;
use App\Models\Wallet;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferActionTestSetUp extends TestCase
{
    use RefreshDatabase;

    protected User $sourceUser;
    protected User $targetUser;
    protected Wallet $sourceWallet;
    protected Wallet $targetWallet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->userSetUp();
        $this->walletSetUp();
        $this->depositSetUp();
    }

    private function userSetUp(): void
    {
        $this->sourceUser = UserFactory::new()->consumer()->create();
        $this->targetUser = UserFactory::new()->consumer()->create();
    }

    private function walletSetUp(): void
    {
        $this->sourceWallet = $this->sourceUser->wallet;
        $this->targetWallet = $this->targetUser->wallet;
    }

    private function depositSetUp(): void
    {
        $this->sourceUser->deposit(amount: '100');
        $this->targetUser->deposit(amount: '100');
    }
}
