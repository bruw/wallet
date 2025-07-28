<?php

namespace App\Actions\Validator;

use App\Constants\Deposit\TransferConstants;
use App\Exceptions\HttpJsonResponseException;
use App\Models\Wallet;
use Symfony\Component\HttpFoundation\Response;

class TransferValidator
{
    public function __construct(
        private readonly string $amount,
        private ?Wallet $sourceWallet = null,
    ) {}

    /**
     * Creates a new instance of DepositValidator.
     */
    public static function for(string $amount, ?Wallet $sourceWallet = null): self
    {
        return new self($amount, $sourceWallet);
    }

    /**
     * Validates if the given amount is numeric.
     */
    public function amountMustBeNumeric(): self
    {
        throw_unless(is_numeric($this->amount), new HttpJsonResponseException(
            trans('actions.transfer.errors.numeric'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given amount is at least the minimum required amount.
     * https://www.php.net/manual/pt_BR/function.bccomp.php
     */
    public function amountMustBeAtLeastMinimum(): self
    {
        $min = TransferConstants::MIN_VALUE;
        $isGreaterThanMinimum = bccomp($this->amount, $min, 2) >= 0;

        throw_unless($isGreaterThanMinimum, new HttpJsonResponseException(
            trans('actions.transfer.errors.min', ['amount' => $min]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given amount does not exceed the maximum allowed amount.
     */
    public function amountMustNotExceedMaximum(): self
    {
        $max = TransferConstants::MAX_VALUE;
        $isLessThanOrEqualMaximum = bccomp($this->amount, $max, 2) <= 0;

        throw_unless($isLessThanOrEqualMaximum, new HttpJsonResponseException(
            trans('actions.transfer.errors.max', ['amount' => $max]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the source wallet has enough balance to make the transfer.
     */
    public function sourceWalletMustHaveEnoughBalance(): self
    {
        $hasBalance = $this->sourceWallet->balance >= $this->amount;

        throw_unless($hasBalance, new HttpJsonResponseException(
            trans('actions.transfer.errors.not_enough_balance'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
