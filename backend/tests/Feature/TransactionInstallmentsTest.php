<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionInstallmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_parent_transaction_and_children_installments(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create([
            'user_id' => $user->id,
            'name' => 'Cartão',
            'type' => 'despesa',
        ]);

        $payload = [
            'category_id' => $category->id,
            'description' => 'Compra parcelada',
            'total_amount' => 100.00,
            'first_date' => '2026-05-10',
            'installments' => 3,
            'type' => 'debito',
            'status' => 'pendente',
            'is_credit_card' => true,
        ];

        $response = $this->postJson('/api/transactions/installments', $payload);
        $response->assertStatus(201);

        $this->assertDatabaseCount('transactions', 4);

        $parent = Transaction::query()->whereNull('parent_id')->firstOrFail();
        $this->assertEquals(100.00, (float) $parent->amount);
        $this->assertSame(3, $parent->installment_total);

        $sum = Transaction::where('parent_id', $parent->id)->sum('amount');
        $this->assertEquals(100.00, (float) $sum);

        $dates = Transaction::where('parent_id', $parent->id)
            ->orderBy('installment_number')
            ->pluck('date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->all();

        $this->assertSame(['2026-05-10', '2026-06-10', '2026-07-10'], $dates);
    }
}
