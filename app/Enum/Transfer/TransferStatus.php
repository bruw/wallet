<?php

namespace App\Enum\Transfer;

enum TransferStatus: string
{
    case COMPLETED = 'completed';
    case CANCELED = 'cancelled';
    case PENDING = 'pending';

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

    /**
     * Determine if the given status is 'pending'.
     */
    public function isPending(): bool
    {
        return $this->value === self::PENDING->value;
    }
}
