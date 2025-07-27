<?php

namespace App\Actions\Validator;

use App\Constants\Deposit\DepositConstants;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DepositValidator
{
    public function __construct(
        private readonly User $user
    ) {}

    /**
     * Creates a new instance of DepositValidator.
     */
    public static function for(User $user): self
    {
        return new self($user);
    }

    /**
     * Validates if the user is not blocked.
     */
    public function userMustNotBeBlocked(): self
    {
        $isBlocked = $this->user->wallet->status->isBlocked();

        throw_if($isBlocked, new HttpJsonResponseException(
            trans('deposit_validator.wallet.blocked'),
            Response::HTTP_UNAUTHORIZED
        ));

        return $this;
    }

    /**
     * Validates if the given amount is numeric.
     */
    public function amountMustBeNumeric(string $amount): self
    {
        throw_unless(is_numeric($amount), new HttpJsonResponseException(
            trans('deposit_validator.amount.numeric'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given amount is at least the minimum required amount.
     * https://www.php.net/manual/pt_BR/function.bccomp.php
     */
    public function amountMustBeAtLeastMinimum(string $amount): self
    {
        $minimum = DepositConstants::MIN_VALUE;
        $isGreaterThanMinimum = bccomp($amount, $minimum, 2) >= 0;

        throw_unless($isGreaterThanMinimum, new HttpJsonResponseException(
            trans('deposit_validator.amount.min', ['amount' => $minimum]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given amount does not exceed the maximum allowed amount.
     */
    public function amountMustNotExceedMaximum(string $amount): self
    {
        $maximum = DepositConstants::MAX_VALUE;
        $isLessThanOrEqualMaximum = bccomp($amount, $maximum, 2) <= 0;

        throw_unless($isLessThanOrEqualMaximum, new HttpJsonResponseException(
            trans('deposit_validator.amount.max', ['amount' => $maximum]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
