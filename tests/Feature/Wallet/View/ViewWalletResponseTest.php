<?php

namespace Tests\Feature\Wallet\View;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class ViewWalletResponseTest extends ViewWalletTestSetUp
{
    public function test_should_return_the_expected_structure_in_the_response(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $this->user->wallet->id)
                    ->where('balance', $this->user->wallet->balance)
                    ->has('created_at')
                    ->has('updated_at')
            );
    }
}
