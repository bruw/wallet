<?php

namespace Tests\Feature\Wallet\Deposits;

use App\Constants\Deposit\DepositConstants;
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

    public function test_should_return_an_error_when_amount_field_value_below_the_minimum_value(): void
    {
        Sanctum::actingAs($this->user);

        $minimum = DepositConstants::MIN_VALUE;
        $amount = bcsub($minimum, '0.1', 2);

        $this->postJson($this->route(), ['amount' => $amount])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.min.numeric', [
                        'attribute' => trans('validation.attributes.amount'),
                        'min' => $minimum,
                    ]))
            );
    }

    public function test_should_return_an_error_when_amount_field_value_above_the_maximum_value(): void
    {
        Sanctum::actingAs($this->user);

        $max = DepositConstants::MAX_VALUE;
        $amount = bcadd($max, '0.1', 2);

        $this->postJson($this->route(), ['amount' => $amount])
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.amount.0', trans('validation.max.numeric', [
                        'attribute' => trans('validation.attributes.amount'),
                        'max' => $max,
                    ]))
            );
    }
}
