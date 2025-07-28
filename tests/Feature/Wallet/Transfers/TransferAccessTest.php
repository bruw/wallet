<?php

namespace Tests\Feature\Wallet\Transfers;

use App\Models\Transfer;
use Database\Factories\UserFactory;

class TransferAccessTest extends TransferTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_able_to_make_transfers(): void
    {
        $this->assertAccessUnauthorizedTo(route: $this->route(), httpVerb: 'post');
    }

    public function test_an_authenticated_consumer_user_should_be_authorized_to_make_transfers(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            params: [
                'amount' => 100,
                'target_key' => $this->targetPublicKey,
            ],
            users: [$this->sourceUser],
        );
    }

    public function test_a_user_who_is_not_a_consumer_should_not_be_able_to_make_transfers(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            params: [
                'amount' => 100,
                'target_key' => $this->targetPublicKey,
            ],
            users: [UserFactory::new()->create()],
        );
    }
}
