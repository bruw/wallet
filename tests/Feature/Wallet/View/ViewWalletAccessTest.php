<?php

namespace Tests\Feature\Wallet\View;

use Database\Factories\UserFactory;

class ViewWalletAccessTest extends ViewWalletTestSetUp
{
    public function test_an_unauthenticated_user_should_not_be_able_to_view_their_wallet(): void
    {
        $this->assertAccessUnauthorizedTo(route: $this->route(), httpVerb: 'get');
    }

    public function test_an_authenticated_user_consumer_should_be_able_to_view_their_wallet(): void
    {
        $this->assertAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            assertHttpResponse: 'assertOk',
            users: [$this->user],
        );
    }

    public function test_an_admin_user_should_not_be_able_to_view_wallet(): void
    {
        $this->assertNoAccessTo(
            route: $this->route(),
            httpVerb: 'get',
            users: [UserFactory::new()->admin()->create()],
        );
    }
}
