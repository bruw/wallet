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
    public static function isOperational(string $status): bool
    {
        return $status === self::OPERATIONAL->value;
    }

    /**
     * Determine if the given status is 'blocked'.
     */
    public static function isBlocked(string $status): bool
    {
        return $status === self::BLOCKED->value;
    }
}
