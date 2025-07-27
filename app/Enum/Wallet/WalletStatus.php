<?php

namespace App\Enum\Wallet;

enum WalletStatus: string
{
    case OPERATIONAL = 'operational';
    case BLOCKED = 'blocked';

    /**
     * Retrieve an array of all the string values for each case in the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Determine if the given status is 'operational'.
     */
    public function isOperational(): bool
    {
        return $this->value === self::OPERATIONAL->value;
    }

    /**
     * Determine if the given status is 'blocked'.
     */
    public function isBlocked(): bool
    {
        return $this->value === self::BLOCKED->value;
    }
}
