<?php

namespace Tests\Feature\Wallet\Transfers;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class TransferRulesTest extends TransferTestSetUp
{
    public function test_should_return_an_error_when_the_required_fields_is_null(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $this->postJson($this->route())
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.amount'),
                    ]))
                    ->where('errors.target_key.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.target_key'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_amount_field_value_is_not_numeric(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $this->postJson($this->route(), [
            'amount' => '100.0a',
            'target_key' => $this->targetPublicKey,
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.numeric', [
                        'attribute' => trans('validation.attributes.amount'),
                    ]))
            );
    }

    public function test_should_return_an_error_when_the_target_key_field_does_not_exist(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $this->postJson($this->route(), [
            'amount' => '100.0',
            'target_key' => '123',
        ])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.target_key.0', trans('validation.exists', [
                        'attribute' => trans('validation.attributes.target_key'),
                    ]))
            );
    }
}
