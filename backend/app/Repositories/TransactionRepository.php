<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseModel implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function forUser(int|string $userId): Collection
    {
        return $this->query()->where('user_id', $userId)->with('category')->get();
    }

    public function forUserGrouped(int|string $userId): Collection
    {
        return $this->query()
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->with([
                'category',
                'children' => function ($query) {
                    $query->orderBy('installment_number')->with('category');
                },
            ])
            ->orderByDesc('date')
            ->get();
    }

    public function queryByUser(int|string $userId): Builder
    {
        return $this->query()->where('user_id', $userId);
    }

    public function createInstallmentParent(int|string $userId, array $data): Transaction
    {
        return DB::transaction(function () use ($userId, $data) {
            $installments = (int) $data['installments'];
            $totalAmount = (string) $data['total_amount'];

            $parent = Transaction::create([
                'user_id' => $userId,
                'category_id' => $data['category_id'] ?? null,
                'description' => $data['description'],
                'installment_total' => $installments,
                'amount' => $totalAmount,
                'date' => $data['first_date'],
                'type' => $data['type'],
                'status' => $data['status'] ?? 'pendente',
                'observations' => $data['observations'] ?? null,
                'is_credit_card' => (bool) ($data['is_credit_card'] ?? false),
            ]);

            $startDate = Carbon::parse($data['first_date'])->startOfDay();
            $totalCents = (int) round(((float) $totalAmount) * 100);
            $base = intdiv($totalCents, $installments);
            $remainder = $totalCents % $installments;

            for ($i = 1; $i <= $installments; $i++) {
                $cents = $base + ($i <= $remainder ? 1 : 0);
                $amount = $cents / 100;

                Transaction::create([
                    'user_id' => $userId,
                    'category_id' => $data['category_id'] ?? null,
                    'parent_id' => $parent->id,
                    'installment_number' => $i,
                    'installment_total' => $installments,
                    'description' => $data['description'],
                    'amount' => $amount,
                    'date' => $startDate->copy()->addMonthsNoOverflow($i - 1)->toDateString(),
                    'type' => $data['type'],
                    'status' => $data['status'] ?? 'pendente',
                    'observations' => $data['observations'] ?? null,
                    'is_credit_card' => (bool) ($data['is_credit_card'] ?? false),
                ]);
            }

            return $parent->load(['category', 'children.category']);
        });
    }
}
