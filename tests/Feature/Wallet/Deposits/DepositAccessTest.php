<?php

namespace Tests\Feature\Wallet\Deposits;

use Database\Factories\UserFactory;

class DepositAccessTest extends DepositTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_able_to_make_deposits(): void
    {
        $this->assertAccessUnauthorizedTo(route: $this->route(), httpVerb: 'post');
    }

    public function test_an_authenticated_consumer_user_should_be_authorized_to_make_deposits(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            assertHttpResponse: 'assertCreated',
            params: ['amount' => 100],
            users: [$this->user],
        );
    }

    public function test_a_user_who_is_not_a_consumer_should_not_be_able_to_make_deposits(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'post',
            params: ['amount' => 100],
            users: [UserFactory::new()->create()],
        );
    }
}
