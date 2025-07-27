<?php

namespace App\Actions\Wallet\Deposit;

use App\Actions\Validator\DepositValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Deposit;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DepositAction
{
    public function __construct(
        private readonly User $user,
        private readonly string $amount
    ) {}

    public function execute(): Deposit
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $deposit = $this->deposit();
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
        DepositValidator::for($this->user)
            ->userMustNotBeBlocked()
            ->amountMustBeNumeric($this->amount)
            ->amountMustBeAtLeastMinimum($this->amount);
    }

    /**
     * Deposits the given amount into the user's wallet.
     */
    private function deposit(): Deposit
    {
        return Deposit::create(['amount' => $this->amount]);
    }

    /**
     * Logs a success message when a deposit is made.
     */
    private function logSuccess(User $user): void
    {
        Log::info("The user {$this->user->name} made a deposit.", [
            'user_id' => $user->id,
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
            trans('actions.user.errors.deposit'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
