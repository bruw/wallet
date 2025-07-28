<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class WalletKey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['public_key'];

    /*
    ================= ** Relationships ** =========================================================
    */

    /**
     * Get the wallet associated with the wallet key.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /*
    ================= ** Helpers ** =========================================================
    */

    /**
     * Generates a random 64-character key.
     */
    public static function generateRandomKey(?int $depth = 0): string
    {
        throw_if($depth > 5, new RuntimeException(
            'Maximum depth reached in random wallet key generation.'
        ));

        $key = bin2hex(random_bytes(16));
        $isUnique = ! WalletKey::where('public_key', $key)->exists();

        return $isUnique ? $key : self::generateRandomKey($depth + 1);
    }
}
