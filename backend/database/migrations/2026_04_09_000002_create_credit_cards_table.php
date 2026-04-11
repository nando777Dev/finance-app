<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['credito', 'debito'])->default('credito');
            $table->string('brand')->nullable();
            $table->string('last4', 4)->nullable();
            $table->decimal('limit_amount', 15, 2)->nullable();
            $table->enum('limit_type', ['total', 'mensal', 'sem_limite'])->default('sem_limite');
            $table->unsignedTinyInteger('due_day')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};

