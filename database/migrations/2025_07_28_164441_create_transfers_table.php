<?php

use App\Enum\Transfer\TransferStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_wallet_id')->constrained('wallets');
            $table->foreignId('target_wallet_id')->constrained('wallets');
            $table->decimal('amount', 10, 2)->unsigned();
            $table->enum('status', TransferStatus::values())->default(TransferStatus::COMPLETED->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
