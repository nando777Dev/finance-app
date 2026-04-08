<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('transactions')->onDelete('cascade');
            }

            if (! Schema::hasColumn('transactions', 'is_credit_card')) {
                $table->boolean('is_credit_card')->default(false);
            }
        });

        if ($driver !== 'sqlite') {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'transaction_series_id')) {
                    $table->dropConstrainedForeignId('transaction_series_id');
                    $table->dropColumn('transaction_series_id');
                }
            });
        }

        if ($driver !== 'sqlite' && Schema::hasTable('transaction_series')) {
            Schema::drop('transaction_series');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

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

        Schema::table('transactions', function (Blueprint $table) use ($driver) {
            if (! Schema::hasColumn('transactions', 'transaction_series_id')) {
                $table->foreignId('transaction_series_id')->nullable()->constrained('transaction_series')->onDelete('cascade');
            }

            if (Schema::hasColumn('transactions', 'parent_id')) {
                if ($driver !== 'sqlite') {
                    $table->dropConstrainedForeignId('parent_id');
                }
                $table->dropColumn('parent_id');
            }

            if (Schema::hasColumn('transactions', 'is_credit_card')) {
                $table->dropColumn('is_credit_card');
            }
        });
    }
};
