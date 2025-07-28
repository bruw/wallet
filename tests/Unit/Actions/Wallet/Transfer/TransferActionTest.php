<?php

namespace Tests\Unit\Actions\Wallet\Transfer;

use App\Constants\Transfer\TransferConstants;
use App\Enum\Transfer\TransferStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Transfer;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TransferActionTest extends TransferActionTestSetUp
{
    public function test_should_return_an_instance_of_transfer(): void
    {
        $transfer = $this->sourceUser->transfer('100', $this->targetWallet);

        $this->assertInstanceOf(Transfer::class, $transfer);
    }

    public function test_should_persist_the_transfer(): void
    {
        $transfer = $this->sourceUser->transfer('100', $this->targetWallet);

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'source_wallet_id' => $this->sourceWallet->id,
            'target_wallet_id' => $this->targetWallet->id,
            'amount' => '100',
            'status' => TransferStatus::COMPLETED,
        ]);
    }

    public function test_should_increment_the_target_wallet_balance(): void
    {
        $this->assertEquals('100.00', $this->targetWallet->balance);

        $this->sourceUser->transfer('50', $this->targetWallet);
        $this->assertEquals('150.00', $this->targetWallet->balance);

        $this->sourceUser->transfer('10', $this->targetWallet);
        $this->assertEquals('160.00', $this->targetWallet->balance);
    }

    public function test_should_decrement_the_source_wallet_balance(): void
    {
        $this->assertEquals('100.00', $this->sourceWallet->balance);

        $this->sourceUser->transfer('50', $this->targetWallet);
        $this->assertEquals('50.00', $this->sourceWallet->balance);

        $this->sourceUser->transfer('49', $this->targetWallet);
        $this->assertEquals('1.00', $this->sourceWallet->balance);
    }

    public function test_should_possible_to_transfer_the_minimum_amount(): void
    {
        $transfer = $this->sourceUser->transfer(TransferConstants::MIN_VALUE, $this->targetWallet);

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'source_wallet_id' => $this->sourceWallet->id,
            'target_wallet_id' => $this->targetWallet->id,
            'amount' => TransferConstants::MIN_VALUE,
            'status' => TransferStatus::COMPLETED,
        ]);
    }

    public function test_should_possible_to_deposit_the_max_amount(): void
    {
        $this->sourceUser->deposit(TransferConstants::MAX_VALUE);
        $transfer = $this->sourceUser->transfer(TransferConstants::MAX_VALUE, $this->targetWallet);

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'source_wallet_id' => $this->sourceWallet->id,
            'target_wallet_id' => $this->targetWallet->id,
            'amount' => TransferConstants::MAX_VALUE,
            'status' => TransferStatus::COMPLETED,
        ]);
    }

    public function test_should_throw_an_exception_when_amount_is_not_numeric(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.transfer.errors.numeric'));

        $this->sourceUser->transfer('abc', $this->targetWallet);
    }

    public function test_should_throw_an_exception_when_amount_is_less_than_the_minimum(): void
    {
        $min = TransferConstants::MIN_VALUE;

        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.transfer.errors.min', [
            'amount' => $min,
        ]));

        $this->sourceUser->transfer(bcsub($min, '0.1', 2), $this->targetWallet);
    }

    public function test_should_throw_an_exception_when_amount_is_greater_than_the_maximum(): void
    {
        $max = TransferConstants::MAX_VALUE;

        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.transfer.errors.max', [
            'amount' => $max,
        ]));

        $this->sourceUser->transfer(bcadd($max, '0.1', 2), $this->targetWallet);
    }

    public function test_should_throw_an_exception_when_an_internal_server_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.transfer.errors.fail'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->sourceUser->transfer('100', $this->targetWallet);
    }
}
