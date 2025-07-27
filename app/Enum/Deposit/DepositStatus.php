<?php

namespace App\Enum\Deposit;

enum DepositStatus: string
{
    case COMPLETED = 'completed';
    case CANCELED = 'cancelled';

    /**
     * Retrieve an array of all the string values for each case in the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Determine if the given status is 'completed'.
     */
    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED->value;
    }

    /**
     * Determine if the given status is 'canceled'.
     */
    public function isCanceled(): bool
    {
        return $this->value === self::CANCELED->value;
    }
}
