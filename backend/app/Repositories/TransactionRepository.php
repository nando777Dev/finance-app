<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            $interval = $data['installment_interval'] ?? $data['interval'] ?? 'monthly';
            $interval = in_array($interval, ['monthly', 'yearly', 'weekly', 'biweekly', 'custom'], true) ? $interval : 'monthly';
            $intervalDays = isset($data['interval_days']) ? (int) $data['interval_days'] : null;

            $hasKind = Schema::hasColumn('transactions', 'kind');
            $hasTransferGroup = Schema::hasColumn('transactions', 'transfer_group');

            $parentData = [
                'user_id' => $userId,
                'category_id' => $data['category_id'] ?? null,
                'goal_id' => $data['goal_id'] ?? null,
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'credit_card_id' => $data['credit_card_id'] ?? null,
                'description' => $data['description'],
                'installment_total' => $installments,
                'installment_interval' => $interval,
                'amount' => $totalAmount,
                'date' => $data['first_date'],
                'type' => $data['type'],
                'status' => $data['status'] ?? 'pendente',
                'observations' => $data['observations'] ?? null,
                'is_credit_card' => (bool) ($data['is_credit_card'] ?? ($data['credit_card_id'] ?? false)),
            ];

            if ($hasKind) {
                $parentData['kind'] = $data['kind'] ?? 'regular';
            }

            if ($hasTransferGroup) {
                $parentData['transfer_group'] = $data['transfer_group'] ?? null;
            }

            $parent = Transaction::create($parentData);

            $startDate = Carbon::parse($data['first_date'])->startOfDay();
            $totalCents = (int) round(((float) $totalAmount) * 100);
            $base = intdiv($totalCents, $installments);
            $remainder = $totalCents % $installments;

            for ($i = 1; $i <= $installments; $i++) {
                $cents = $base + ($i <= $remainder ? 1 : 0);
                $amount = $cents / 100;

                $dueDate = $startDate->copy();
                if ($interval === 'yearly') {
                    $dueDate = $dueDate->addYearsNoOverflow($i - 1);
                } elseif ($interval === 'monthly') {
                    $dueDate = $dueDate->addMonthsNoOverflow($i - 1);
                } elseif ($interval === 'weekly') {
                    $dueDate = $dueDate->addWeeks($i - 1);
                } elseif ($interval === 'biweekly') {
                    $dueDate = $dueDate->addWeeks(2 * ($i - 1));
                } else {
                    $days = max(1, (int) ($intervalDays ?? 1));
                    $dueDate = $dueDate->addDays($days * ($i - 1));
                }

                $childData = [
                    'user_id' => $userId,
                    'category_id' => $data['category_id'] ?? null,
                    'goal_id' => $data['goal_id'] ?? null,
                    'bank_account_id' => $data['bank_account_id'] ?? null,
                    'credit_card_id' => $data['credit_card_id'] ?? null,
                    'parent_id' => $parent->id,
                    'installment_number' => $i,
                    'installment_total' => $installments,
                    'installment_interval' => $interval,
                    'description' => $data['description'],
                    'amount' => $amount,
                    'date' => $dueDate->toDateString(),
                    'type' => $data['type'],
                    'status' => $data['status'] ?? 'pendente',
                    'observations' => $data['observations'] ?? null,
                    'is_credit_card' => (bool) ($data['is_credit_card'] ?? ($data['credit_card_id'] ?? false)),
                ];

                if ($hasKind) {
                    $childData['kind'] = $data['kind'] ?? 'regular';
                }

                if ($hasTransferGroup) {
                    $childData['transfer_group'] = $data['transfer_group'] ?? null;
                }

                Transaction::create($childData);
            }

            return $parent->load(['category', 'children.category']);
        });
    }
}
