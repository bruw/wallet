<?php

namespace App\Actions\Validator;

use App\Constants\Deposit\DepositConstants;
use App\Exceptions\HttpJsonResponseException;
use Symfony\Component\HttpFoundation\Response;

class DepositValidator
{
    public function __construct(
        private readonly string $amount
    ) {}

    /**
     * Creates a new instance of DepositValidator.
     */
    public static function for(string $amount): self
    {
        return new self($amount);
    }

    /**
     * Validates if the given amount is numeric.
     */
    public function amountMustBeNumeric(): self
    {
        throw_unless(is_numeric($this->amount), new HttpJsonResponseException(
            trans('actions.deposit.errors.numeric'),
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
        $min = DepositConstants::MIN_VALUE;
        $isGreaterThanMinimum = bccomp($this->amount, $min, 2) >= 0;

        throw_unless($isGreaterThanMinimum, new HttpJsonResponseException(
            trans('actions.deposit.errors.min', ['amount' => $min]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }

    /**
     * Validates if the given amount does not exceed the maximum allowed amount.
     */
    public function amountMustNotExceedMaximum(): self
    {
        $max = DepositConstants::MAX_VALUE;
        $isLessThanOrEqualMaximum = bccomp($this->amount, $max, 2) <= 0;

        throw_unless($isLessThanOrEqualMaximum, new HttpJsonResponseException(
            trans('actions.deposit.errors.max', ['amount' => $max]),
            Response::HTTP_UNPROCESSABLE_ENTITY
        ));

        return $this;
    }
}
