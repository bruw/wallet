<?php

namespace App\Actions\Wallet\Transfer;

use App\Actions\Validator\TransferValidator;
use App\Actions\Validator\UserValidator;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TransferAction
{
    private Wallet $sourceWallet;

    public function __construct(
        private readonly User $user,
        private readonly string $amount,
        private Wallet $targetWallet
    ) {
        $this->sourceWallet = $this->user->wallet;
    }

    public function execute(): Transfer
    {
        $this->validateAttributesBeforeAction();

        try {
            return DB::transaction(function () {
                $transfer = $this->createTransfer();

                $this->decrementSourceWalletBalance($transfer);
                $this->incrementTargetWalletBalance($transfer);
                $this->logSuccess();

                return $transfer;
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
        UserValidator::for($this->user)
            ->mustOwnWallet($this->sourceWallet);

        TransferValidator::for($this->amount, $this->sourceWallet)
            ->amountMustBeNumeric()
            ->amountMustBeAtLeastMinimum()
            ->amountMustNotExceedMaximum()
            ->sourceWalletMustHaveEnoughBalance();
    }

    /**
     * Creates a new transfer and returns the created transfer model.
     */
    private function createTransfer(): Transfer
    {
        return Transfer::create([
            'source_wallet_id' => $this->sourceWallet->id,
            'target_wallet_id' => $this->targetWallet->id,
            'amount' => $this->amount,
        ]);
    }

    /**
     * Decrements the source wallet balance by the given transfer amount.
     */
    private function decrementSourceWalletBalance(Transfer $transfer): void
    {
        $this->sourceWallet->balance -= $transfer->amount;
        $this->sourceWallet->save();
    }

    /**
     * Increments the target wallet balance by the given transfer amount.
     */
    private function incrementTargetWalletBalance(Transfer $transfer): void
    {
        $this->targetWallet->balance += $transfer->amount;
        $this->targetWallet->save();
    }

    /**
     * Logs a success message when a transfer is made.
     */
    private function logSuccess(): void
    {
        Log::info("The user {$this->user->name} made a transfer.", [
            'user_id' => $this->user->id,
            'target_user_id' => $this->targetWallet->user->id,
            'amount' => $this->amount,
        ]);
    }

    /**
     * Handles exceptions that occur during a transfer.
     */
    private function handleException(Exception $e): void
    {
        Log::error("The user {$this->user->name} attempted to make a transfer, but an error occurred.", [
            'user_id' => $this->user->id,
            'target_user_id' => $this->targetWallet->user->id,
            'amount' => $this->amount,
            'errors' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ],
        ]);

        throw new HttpJsonResponseException(
            trans('actions.transfer.errors.fail'),
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
