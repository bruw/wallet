<?php

namespace Tests\Unit\Actions\Wallet\Deposit;

use App\Constants\Deposit\DepositConstants;
use App\Enum\Deposit\DepositStatus;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Deposit;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DepositActionTest extends DepositActionTestSetUp
{
    public function test_should_return_an_instance_of_deposit(): void
    {
        $this->assertInstanceOf(Deposit::class, $this->user->deposit(amount: '100'));
    }

    public function test_should_persist_the_deposit(): void
    {
        $deposit = $this->user->deposit(amount: '100');

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'wallet_id' => $this->user->wallet->id,
            'amount' => '100',
            'status' => DepositStatus::COMPLETED,
        ]);
    }

    public function test_should_increment_the_wallet_balance(): void
    {
        $this->assertEquals('0.00', $this->user->wallet->balance);

        $this->user->deposit(amount: '10');
        $this->assertEquals('10.00', $this->user->wallet->balance);

        $this->user->deposit(amount: '1000');
        $this->assertEquals('1010.00', $this->user->wallet->balance);
    }

    public function test_should_possible_to_deposit_the_minimum_amount(): void
    {
        $deposit = $this->user->deposit(amount: DepositConstants::MIN_VALUE);

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'wallet_id' => $this->user->wallet->id,
            'amount' => DepositConstants::MIN_VALUE,
            'status' => DepositStatus::COMPLETED,
        ]);
    }

    public function test_should_possible_to_deposit_the_max_amount(): void
    {
        $deposit = $this->user->deposit(amount: DepositConstants::MAX_VALUE);

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'wallet_id' => $this->user->wallet->id,
            'amount' => DepositConstants::MAX_VALUE,
            'status' => DepositStatus::COMPLETED,
        ]);
    }

    public function test_should_throw_an_exception_when_the_wallet_is_blocked(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.user.errors.wallet.blocked'));

        $this->user->wallet->update(['blocked' => true]);
        $this->user->deposit(amount: '100');
    }

    public function test_should_throw_an_exception_when_amount_is_not_numeric(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.deposit.errors.numeric'));

        $this->user->deposit(amount: '100a');
    }

    public function test_should_throw_an_exception_when_amount_is_less_than_the_minimum(): void
    {
        $minimum = DepositConstants::MIN_VALUE;

        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.deposit.errors.min', [
            'amount' => $minimum,
        ]));

        $this->user->deposit(amount: bcsub($minimum, '0.1', 2));
    }

    public function test_should_throw_an_exception_when_amount_is_greater_than_the_maximum(): void
    {
        $max = DepositConstants::MAX_VALUE;

        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('actions.deposit.errors.max', [
            'amount' => $max,
        ]));

        $this->user->deposit(amount: bcadd($max, '0.1', 2));
    }

    public function test_should_throw_an_exception_when_an_internal_server_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.deposit.errors.fail'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->user->deposit(amount: '100');
    }
}
