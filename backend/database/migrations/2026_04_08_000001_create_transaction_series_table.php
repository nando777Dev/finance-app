<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('kind')->default('installment');
            $table->string('description');
            $table->decimal('total_amount', 15, 2);
            $table->date('start_date');
            $table->unsignedSmallInteger('installment_total');
            $table->string('interval')->default('monthly');
            $table->enum('type', ['credito', 'debito']);
            $table->enum('status', ['pago', 'pendente'])->default('pendente');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_series');
    }
};
