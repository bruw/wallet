<?php

namespace App\Models;

use App\Enum\Deposit\DepositStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'amount',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => DepositStatus::class,
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'status' => DepositStatus::COMPLETED,
    ];

    /**
     * The wallet that owns the Deposit.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
