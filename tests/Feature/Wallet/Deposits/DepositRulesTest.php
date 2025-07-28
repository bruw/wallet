<?php

namespace Tests\Feature\Wallet\Deposits;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class DepositRulesTest extends DepositTestSetUp
{
    public function test_should_return_an_error_when_amount_field_value_is_null(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.amount'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_amount_field_value_is_not_numeric(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson($this->route(), ['amount' => '100.0a'])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.numeric', [
                        'attribute' => trans('validation.attributes.amount'),
                    ]))
            );
    }
}
