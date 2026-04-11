<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class DashboardStats extends Component
{
    public string $preset = 'month';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public float $incomeCurrent = 0.0;

    public float $expenseCurrent = 0.0;

    public float $balanceCurrent = 0.0;

    public ?float $incomeDeltaPercent = null;

    public ?float $expenseDeltaPercent = null;

    public ?float $balanceDeltaPercent = null;

    public int $pendingInstallments = 0;

    public array $topExpenseCategories = [];

    public array $monthlyExpenseTrend = [];

    public array $monthlyFlowTrend = [];

    public array $expenseByOrigin = [];

    public array $benefitUsage = [];

    public array $bankAccountBalances = [];

    public float $bankAccountTotal = 0.0;

    public array $recentTransactions = [];

    public function mount(): void
    {
        $this->preset = 'month';
        $this->setDatesFromPreset();
        $this->loadStats();
    }

    public function updatedPreset(): void
    {
        if ($this->preset !== 'custom') {
            $this->setDatesFromPreset();
            $this->loadStats();
        }
    }

    public function updatedStartDate(): void
    {
        if ($this->preset !== 'custom') {
            return;
        }

        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $this->loadStats();
    }

    public function updatedEndDate(): void
    {
        if ($this->preset !== 'custom') {
            return;
        }

        if (! $this->startDate || ! $this->endDate) {
            return;
        }

        $this->loadStats();
    }

    public function applyFilter(): void
    {
        if ($this->preset !== 'custom') {
            return;
        }

        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }

    private function userId(): int
    {
        $id = Auth::id();
        if (! $id) {
            abort(401);
        }

        return (int) $id;
    }

    private function deltaPercent(float $previous, float $current): ?float
    {
        if ($previous == 0.0) {
            return $current == 0.0 ? 0.0 : null;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function buildMonthlyExpenseTrend(int $userId, int $months): array
    {
        $trend = [];
        [$start, $end] = $this->selectedRange();
        $cursor = $end->copy()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $expense = (float) $this->baseQuery($userId)
                ->where('user_id', $userId)
                ->where('type', 'debito')
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $trend[] = [
                'label' => $cursor->format('M/Y'),
                'value' => $expense,
            ];

            $cursor->addMonth();
        }

        return $trend;
    }

    private function buildMonthlyFlowTrend(int $userId, int $months): array
    {
        $trend = [];
        [, $end] = $this->selectedRange();
        $cursor = $end->copy()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $income = (float) $this->baseQuery($userId)
                ->where('user_id', $userId)
                ->where('type', 'credito')
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $expense = (float) $this->baseQuery($userId)
                ->where('user_id', $userId)
                ->where('type', 'debito')
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $trend[] = [
                'label' => $cursor->format('M/Y'),
                'income' => $income,
                'expense' => $expense,
            ];

            $cursor->addMonth();
        }

        return $trend;
    }

    private function pendingInstallmentsCount(int $userId): int
    {
        if (! Schema::hasColumn('transactions', 'installment_number')) {
            return 0;
        }

        if (Schema::hasColumn('transactions', 'parent_id')) {
            return (int) Transaction::query()
                ->where('user_id', $userId)
                ->whereNotNull('parent_id')
                ->whereNotNull('installment_number')
                ->where('status', 'pendente')
                ->whereDate('date', '>=', Carbon::today()->toDateString())
                ->count();
        }

        if (Schema::hasColumn('transactions', 'transaction_series_id')) {
            return (int) Transaction::query()
                ->where('user_id', $userId)
                ->whereNotNull('transaction_series_id')
                ->whereNotNull('installment_number')
                ->where('status', 'pendente')
                ->whereDate('date', '>=', Carbon::today()->toDateString())
                ->count();
        }

        return 0;
    }

    private function baseQuery(int $userId)
    {
        $q = Transaction::query();

        if (Schema::hasColumn('transactions', 'kind')) {
            $q->where(function ($qq) {
                $qq->whereNull('kind')
                    ->orWhere('kind', 'regular');
            });
        }

        if ($this->excludeInstallmentParentsEnabled()) {
            $q->where(function ($qq) {
                $qq->whereNotNull('parent_id')
                    ->orWhereNull('installment_total');
            });
        }

        return $q;
    }

    private function excludeInstallmentParentsEnabled(): bool
    {
        return Schema::hasColumn('transactions', 'parent_id')
            && Schema::hasColumn('transactions', 'installment_total')
            && Schema::hasColumn('transactions', 'installment_number');
    }

    private function buildExpenseByOrigin(int $userId, Carbon $start, Carbon $end): array
    {
        if (! Schema::hasColumn('transactions', 'bank_account_id') || ! Schema::hasColumn('transactions', 'credit_card_id')) {
            return [];
        }

        $base = $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->where('type', 'debito')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        $cash = (float) (clone $base)->whereNull('bank_account_id')->whereNull('credit_card_id')->sum('amount');
        $bank = (float) (clone $base)->whereNotNull('bank_account_id')->sum('amount');
        $card = (float) (clone $base)->whereNotNull('credit_card_id')->sum('amount');

        $total = $cash + $bank + $card;
        $pct = fn (float $value) => $total > 0 ? ($value / $total) * 100 : 0.0;

        return [
            [
                'key' => 'cash',
                'label' => 'Dinheiro',
                'total' => $cash,
                'percent' => $pct($cash),
                'bar' => 'bg-slate-700',
                'badge' => 'bg-slate-50 text-slate-700',
            ],
            [
                'key' => 'bank',
                'label' => 'Contas bancárias',
                'total' => $bank,
                'percent' => $pct($bank),
                'bar' => 'bg-brand-700',
                'badge' => 'bg-brand-600/10 text-brand-700',
            ],
            [
                'key' => 'card',
                'label' => 'Cartões',
                'total' => $card,
                'percent' => $pct($card),
                'bar' => 'bg-red-600',
                'badge' => 'bg-red-50 text-red-700',
            ],
        ];
    }

    private function buildBenefitUsage(int $userId, Carbon $start, Carbon $end): array
    {
        if (! Schema::hasTable('credit_cards') || ! Schema::hasColumn('transactions', 'credit_card_id')) {
            return [];
        }

        $cards = DB::table('credit_cards')
            ->where('user_id', $userId)
            ->where('type', 'debito')
            ->whereIn('limit_type', ['total', 'mensal'])
            ->whereNotNull('limit_amount')
            ->orderBy('name')
            ->get(['id', 'name', 'brand', 'last4', 'limit_amount', 'limit_type']);

        if ($cards->isEmpty()) {
            return [];
        }

        $ids = $cards->pluck('id')->all();

        $spendQuery = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('type', 'debito')
            ->whereIn('credit_card_id', $ids)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()]);

        if ($this->excludeInstallmentParentsEnabled()) {
            $spendQuery->where(function ($q) {
                $q->whereNotNull('parent_id')
                    ->orWhereNull('installment_total');
            });
        }

        $spentByCard = $spendQuery
            ->groupBy('credit_card_id')
            ->select('credit_card_id', DB::raw('SUM(amount) as total'))
            ->pluck('total', 'credit_card_id');

        $monthlyOverrides = collect();
        if (Schema::hasTable('benefit_monthly_limits')) {
            $ym = [$start->year, $start->month];
            $monthlyOverrides = DB::table('benefit_monthly_limits')
                ->whereIn('credit_card_id', $ids)
                ->where('year', $ym[0])
                ->where('month', $ym[1])
                ->pluck('amount', 'credit_card_id');
        }

        return $cards
            ->map(function ($c) use ($spentByCard, $monthlyOverrides) {
                $id = (int) $c->id;
                $limit = (float) ($monthlyOverrides[$id] ?? $c->limit_amount);
                $spent = (float) ($spentByCard[$id] ?? 0);
                $percent = $limit > 0 ? ($spent / $limit) * 100 : 0;

                return [
                    'id' => $id,
                    'name' => (string) $c->name,
                    'brand' => $c->brand ? (string) $c->brand : null,
                    'last4' => $c->last4 ? (string) $c->last4 : null,
                    'limit_type' => (string) $c->limit_type,
                    'limit_amount' => $limit,
                    'spent' => $spent,
                    'remaining' => max(0, $limit - $spent),
                    'percent' => $percent,
                ];
            })
            ->all();
    }

    private function setDatesFromPreset(): void
    {
        $today = Carbon::today();

        if ($this->preset === 'last_30') {
            $start = $today->copy()->subDays(29);
            $end = $today->copy();
        } elseif ($this->preset === 'last_90') {
            $start = $today->copy()->subDays(89);
            $end = $today->copy();
        } elseif ($this->preset === 'year') {
            $start = $today->copy()->startOfYear();
            $end = $today->copy()->endOfYear();
        } else {
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->endOfMonth();
        }

        $this->startDate = $start->toDateString();
        $this->endDate = $end->toDateString();
    }

    private function selectedRange(): array
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : Carbon::today()->startOfMonth();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : Carbon::today()->endOfMonth();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function previousRange(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();

        return [$prevStart, $prevEnd];
    }

    private function loadStats(): void
    {
        $userId = $this->userId();

        [$currentStart, $currentEnd] = $this->selectedRange();
        [$prevStart, $prevEnd] = $this->previousRange($currentStart, $currentEnd);

        $this->incomeCurrent = (float) $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->where('type', 'credito')
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->sum('amount');

        $this->expenseCurrent = (float) $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->where('type', 'debito')
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->sum('amount');

        $this->balanceCurrent = $this->incomeCurrent - $this->expenseCurrent;

        $incomePrev = (float) $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->where('type', 'credito')
            ->whereBetween('date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->sum('amount');

        $expensePrev = (float) $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->where('type', 'debito')
            ->whereBetween('date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->sum('amount');

        $balancePrev = $incomePrev - $expensePrev;

        $this->incomeDeltaPercent = $this->deltaPercent($incomePrev, $this->incomeCurrent);
        $this->expenseDeltaPercent = $this->deltaPercent($expensePrev, $this->expenseCurrent);
        $this->balanceDeltaPercent = $this->deltaPercent($balancePrev, $this->balanceCurrent);

        $this->pendingInstallments = $this->pendingInstallmentsCount($userId);

        $this->topExpenseCategories = DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('categories.user_id', $userId)
            ->where('transactions.type', 'debito')
            ->when($this->excludeInstallmentParentsEnabled(), function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('transactions.parent_id')
                        ->orWhereNull('transactions.installment_total');
                });
            })
            ->whereBetween('transactions.date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->groupBy('categories.id', 'categories.name')
            ->select('categories.id', 'categories.name', DB::raw('SUM(transactions.amount) as total'))
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => (string) $row->name,
                'total' => (float) $row->total,
            ])
            ->all();

        $this->expenseByOrigin = $this->buildExpenseByOrigin($userId, $currentStart, $currentEnd);
        $this->benefitUsage = $this->buildBenefitUsage($userId, $currentStart, $currentEnd);

        $this->monthlyExpenseTrend = $this->buildMonthlyExpenseTrend($userId, 6);
        $this->monthlyFlowTrend = $this->buildMonthlyFlowTrend($userId, 6);

        $this->recentTransactions = $this->baseQuery($userId)
            ->where('user_id', $userId)
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(7)
            ->get()
            ->map(function (Transaction $t) {
                return [
                    'id' => (int) $t->id,
                    'description' => (string) $t->description,
                    'amount' => (float) $t->amount,
                    'date' => optional($t->date)->format('Y-m-d'),
                    'type' => (string) $t->type,
                    'status' => (string) $t->status,
                    'installment_label' => $t->installment_number && $t->installment_total
                        ? ((int) $t->installment_number).'/'.((int) $t->installment_total)
                        : null,
                ];
            })
            ->all();

        [$this->bankAccountBalances, $this->bankAccountTotal] = $this->buildBankAccountBalances($userId, $currentEnd);
    }

    private function buildBankAccountBalances(int $userId, Carbon $end): array
    {
        if (! Schema::hasTable('bank_accounts') || ! Schema::hasColumn('transactions', 'bank_account_id')) {
            return [[], 0.0];
        }

        $hasOpeningBalance = Schema::hasColumn('bank_accounts', 'opening_balance');

        $accounts = DB::table('bank_accounts')
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get($hasOpeningBalance ? ['id', 'name', 'opening_balance'] : ['id', 'name']);

        if ($accounts->isEmpty()) {
            return [[], 0.0];
        }

        $ids = $accounts->pluck('id')->all();

        $moves = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('bank_account_id', $ids)
            ->whereDate('date', '<=', $end->toDateString());

        if ($this->excludeInstallmentParentsEnabled()) {
            $moves->where(function ($q) {
                $q->whereNotNull('parent_id')
                    ->orWhereNull('installment_total');
            });
        }

        $netByAccount = $moves
            ->groupBy('bank_account_id')
            ->select('bank_account_id', DB::raw('SUM(CASE WHEN type = "credito" THEN amount ELSE -amount END) as total'))
            ->pluck('total', 'bank_account_id');

        $rows = [];
        $total = 0.0;

        foreach ($accounts as $acc) {
            $id = (int) $acc->id;
            $opening = $hasOpeningBalance ? (float) ($acc->opening_balance ?? 0) : 0.0;
            $net = (float) ($netByAccount[$id] ?? 0);
            $balance = $opening + $net;

            $rows[] = [
                'id' => $id,
                'name' => (string) $acc->name,
                'balance' => $balance,
            ];

            $total += $balance;
        }

        return [$rows, $total];
    }
}
