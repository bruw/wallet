<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['balance', 'blocked'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'blocked' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'blocked' => false,
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet keys.
     */
    public function keys(): HasMany
    {
        return $this->hasMany(WalletKey::class);
    }

    /**
     * Get the deposits for the wallet.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Determines if the wallet is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->blocked;
    }
}
