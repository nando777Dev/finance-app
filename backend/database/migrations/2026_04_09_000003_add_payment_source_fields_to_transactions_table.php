<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null');
            $table->foreignId('credit_card_id')->nullable()->constrained('credit_cards')->onDelete('set null');
            $table->enum('installment_interval', ['monthly', 'yearly'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('bank_account_id');
            $table->dropConstrainedForeignId('credit_card_id');
            $table->dropColumn('installment_interval');
        });
    }
};
