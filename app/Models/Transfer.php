<?php

namespace App\Models;

use App\Enum\Transfer\TransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'source_wallet_id',
        'target_wallet_id',
        'amount', 
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => TransferStatus::class,
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'status' => TransferStatus::COMPLETED,
    ];

    /*
    ================= ** Relationships ** =========================================================
    */

    /**
     * Get the source wallet of the transfer.
     */
    public function sourceWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, ['source_wallet_id']);
    }

    /**
     * Get the target wallet of the transfer.
     */
    public function targetWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, ['target_wallet_id']);
    }
}
