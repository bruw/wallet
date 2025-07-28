<?php

namespace App\Actions\Wallet\Deposit;

use App\Actions\Validator\DepositValidator;
use App\Actions\Validator\UserValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DepositAction
{
    private Wallet $wallet;

    public function __construct(
        private readonly User $user,
        private readonly string $amount
    ) {
        $this->wallet = $this->user->wallet;
    }

    public function execute(): Deposit
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $deposit = $this->deposit();
                $this->incrementWalletBalance($deposit);
                $this->logSuccess($this->user);

                return $deposit;
            });
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Validates the attributes before the action is executed.
     */
    private function validateAttributesBeforeAction(): void
    {
        UserValidator::for($this->user)->mustNotBeBlocked();

        DepositValidator::for($this->amount)
            ->amountMustBeNumeric()
            ->amountMustBeAtLeastMinimum()
            ->amountMustNotExceedMaximum();
    }

    /**
     * Deposits the given amount into the user's wallet.
     */
    private function deposit(): Deposit
    {
        return $this->wallet->deposits()
            ->create(['amount' => $this->amount]);
    }

    /**
     * Increments the wallet balance by the given deposit amount.
     */
    private function incrementWalletBalance(Deposit $deposit): void
    {
        $this->wallet->balance += $deposit->amount;
        $this->wallet->save();
    }

    /**
     * Logs a success message when a deposit is made.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} made a deposit.", [
            'user_id' => $this->user->id,
            'amount' => $this->amount,
        ]);
    }

    /**
     * Handles exceptions that occur during user deposit.
     */
    private function handleException(Exception $e): void
    {
        Log::error("The user {$this->user->name} attempted to make a deposit, but an error occurred.", [
            'user_id' => $this->user->id,
            'amount' => $this->amount,
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('actions.deposit.errors.fail'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
