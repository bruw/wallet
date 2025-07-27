<?php

namespace App\Actions\Validator;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class DepositValidator
{
    private const MIN_AMOUNT = '5.00';

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
        $isGreaterThanMinimum = bccomp($amount, self::MIN_AMOUNT, 2) >= 0;

        throw_unless($isGreaterThanMinimum, new HttpJsonResponseException(
            trans('deposit_validator.amount.min', ['amount' => self::MIN_AMOUNT]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
