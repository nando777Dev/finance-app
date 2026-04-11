<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefit_monthly_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_card_id')->constrained('credit_cards')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->unique(['credit_card_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefit_monthly_limits');
    }
};

