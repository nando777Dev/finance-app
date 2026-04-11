<?php

namespace App\Livewire\App\Goals;

use App\Models\Goal;
use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Repositories\Contracts\CreditCardRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Goal $goal;

    public float $saved = 0.0;

    public float $remaining = 0.0;

    public float $percent = 0.0;

    public array $monthlyTrend = [];

    public array $recent = [];

    public bool $txModalOpen = false;

    public string $tx_type = 'credito';

    public string $payment_source = 'bank';

    public ?int $bank_account_id = null;

    public ?int $credit_card_id = null;

    public string $amount = '';

    public string $date = '';

    public string $description = '';

    public string $status = 'pago';

    public bool $transferModalOpen = false;

    public ?int $from_bank_account_id = null;

    public string $transfer_amount = '';

    public string $transfer_date = '';

    public string $transfer_description = '';

    public string $transfer_status = 'pago';

    public array $accounts = [];

    public array $cards = [];

    public array $benefitCards = [];

    private TransactionRepositoryInterface $transactions;

    private BankAccountRepositoryInterface $accountsRepo;

    private CreditCardRepositoryInterface $cardsRepo;

    public function boot(
        TransactionRepositoryInterface $transactions,
        BankAccountRepositoryInterface $accounts,
        CreditCardRepositoryInterface $cards
    ): void {
        $this->transactions = $transactions;
        $this->accountsRepo = $accounts;
        $this->cardsRepo = $cards;
    }

    public function mount(Goal $goal): void
    {
        if ((int) $goal->user_id !== $this->userId()) {
            abort(403);
        }

        $this->goal = $goal;
        $this->date = Carbon::today()->toDateString();
        $this->loadData();
        $this->loadSources();
    }

    public function openDeposit(): void
    {
        $this->resetTxForm();
        $this->tx_type = 'credito';
        $this->txModalOpen = true;
    }

    public function openWithdraw(): void
    {
        $this->resetTxForm();
        $this->tx_type = 'debito';
        $this->txModalOpen = true;
    }

    public function openTransfer(): void
    {
        $this->resetTransferForm();
        $this->transferModalOpen = true;
    }

    public function saveTx(): void
    {
        if (! Schema::hasColumn('transactions', 'goal_id')) {
            abort(500);
        }

        $userId = $this->userId();
        $hasBankAccountId = Schema::hasColumn('transactions', 'bank_account_id');
        $hasCreditCardId = Schema::hasColumn('transactions', 'credit_card_id');

        $this->amount = $this->normalizeMoney($this->amount);

        $validated = $this->validate([
            'tx_type' => 'required|in:credito,debito',
            'payment_source' => 'required|in:bank,card,benefit,cash',
            'bank_account_id' => [
                'nullable',
                Rule::requiredIf(fn () => $this->payment_source === 'bank'),
                Rule::exists('bank_accounts', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'credit_card_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($this->payment_source, ['card', 'benefit'], true)),
                Rule::exists('credit_cards', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'status' => 'required|in:pago,pendente',
        ]);

        if ($validated['payment_source'] === 'cash') {
            $validated['bank_account_id'] = null;
            $validated['credit_card_id'] = null;
        } elseif (in_array($validated['payment_source'], ['card', 'benefit'], true)) {
            if (! $hasCreditCardId) {
                abort(500);
            }
            $validated['bank_account_id'] = null;
        } else {
            if (! $hasBankAccountId) {
                abort(500);
            }
            $validated['credit_card_id'] = null;
        }

        $this->transactions->create([
            'user_id' => $userId,
            'goal_id' => $this->goal->id,
            'category_id' => null,
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'credit_card_id' => $validated['credit_card_id'] ?? null,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'type' => $validated['tx_type'],
            'status' => $validated['status'],
            'observations' => null,
        ]);

        $this->txModalOpen = false;
        $this->resetTxForm();
        $this->loadData();
    }

    public function saveTransfer(): void
    {
        if (! Schema::hasColumn('transactions', 'goal_id')) {
            abort(500);
        }
        if (! Schema::hasColumn('transactions', 'bank_account_id')) {
            abort(500);
        }
        if (! Schema::hasColumn('transactions', 'kind') || ! Schema::hasColumn('transactions', 'transfer_group')) {
            abort(500);
        }

        $userId = $this->userId();

        $this->transfer_amount = $this->normalizeMoney($this->transfer_amount);

        $validated = $this->validate([
            'from_bank_account_id' => [
                'required',
                Rule::exists('bank_accounts', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'transfer_amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'transfer_description' => 'required|string|max:255',
            'transfer_status' => 'required|in:pago,pendente',
        ]);

        $group = (string) Str::uuid();
        $amount = $validated['transfer_amount'];
        $date = $validated['transfer_date'];
        $description = $validated['transfer_description'];

        DB::transaction(function () use ($userId, $group, $amount, $date, $description, $validated) {
            $this->transactions->create([
                'user_id' => $userId,
                'category_id' => null,
                'goal_id' => null,
                'bank_account_id' => $validated['from_bank_account_id'],
                'credit_card_id' => null,
                'description' => $description,
                'amount' => $amount,
                'date' => $date,
                'type' => 'debito',
                'status' => $validated['transfer_status'],
                'observations' => null,
                'kind' => 'transfer',
                'transfer_group' => $group,
            ]);

            $this->transactions->create([
                'user_id' => $userId,
                'category_id' => null,
                'goal_id' => $this->goal->id,
                'bank_account_id' => null,
                'credit_card_id' => null,
                'description' => $description,
                'amount' => $amount,
                'date' => $date,
                'type' => 'credito',
                'status' => $validated['transfer_status'],
                'observations' => null,
                'kind' => 'transfer',
                'transfer_group' => $group,
            ]);
        });

        $this->transferModalOpen = false;
        $this->resetTransferForm();
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.app.goals.show');
    }

    private function loadSources(): void
    {
        $userId = $this->userId();

        $this->accounts = Schema::hasTable('bank_accounts')
            ? $this->accountsRepo->query()->where('user_id', $userId)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];

        $this->cards = Schema::hasTable('credit_cards')
            ? $this->cardsRepo->query()->where('user_id', $userId)->orderBy('name')->get(['id', 'name', 'type', 'last4', 'limit_type', 'limit_amount'])->toArray()
            : [];

        $this->benefitCards = array_values(array_filter($this->cards, function ($c) {
            return ($c['type'] ?? null) === 'debito'
                && in_array($c['limit_type'] ?? null, ['total', 'mensal'], true)
                && ($c['limit_amount'] ?? null) !== null;
        }));
    }

    private function loadData(): void
    {
        if (! Schema::hasColumn('transactions', 'goal_id')) {
            $this->saved = 0;
            $this->remaining = (float) $this->goal->target_amount;
            $this->percent = 0;
            $this->monthlyTrend = [];
            $this->recent = [];

            return;
        }

        $userId = $this->userId();

        $savedQuery = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('goal_id', $this->goal->id);

        if ($this->excludeInstallmentParentsEnabled()) {
            $savedQuery->where(function ($q) {
                $q->whereNotNull('parent_id')
                    ->orWhereNull('installment_total');
            });
        }

        $saved = (float) $savedQuery
            ->selectRaw('SUM(CASE WHEN type = "credito" THEN amount ELSE -amount END) as total')
            ->value('total');

        $target = (float) $this->goal->target_amount;
        $this->saved = $saved;
        $this->remaining = max(0, $target - $saved);
        $this->percent = $target > 0 ? ($saved / $target) * 100 : 0;

        $this->monthlyTrend = $this->buildMonthlyTrend($userId, 6);

        $this->recent = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('goal_id', $this->goal->id)
            ->when($this->excludeInstallmentParentsEnabled(), function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('parent_id')
                        ->orWhereNull('installment_total');
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'description', 'amount', 'date', 'type'])
            ->map(fn ($t) => [
                'id' => (int) $t->id,
                'description' => (string) $t->description,
                'amount' => (float) $t->amount,
                'date' => (string) $t->date,
                'type' => (string) $t->type,
            ])
            ->all();
    }

    private function buildMonthlyTrend(int $userId, int $months): array
    {
        $trend = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $income = (float) DB::table('transactions')
                ->where('user_id', $userId)
                ->where('goal_id', $this->goal->id)
                ->where('type', 'credito')
                ->when($this->excludeInstallmentParentsEnabled(), function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNotNull('parent_id')
                            ->orWhereNull('installment_total');
                    });
                })
                ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            $expense = (float) DB::table('transactions')
                ->where('user_id', $userId)
                ->where('goal_id', $this->goal->id)
                ->where('type', 'debito')
                ->when($this->excludeInstallmentParentsEnabled(), function ($q) {
                    $q->where(function ($qq) {
                        $qq->whereNotNull('parent_id')
                            ->orWhereNull('installment_total');
                    });
                })
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

    private function excludeInstallmentParentsEnabled(): bool
    {
        return Schema::hasColumn('transactions', 'parent_id')
            && Schema::hasColumn('transactions', 'installment_total')
            && Schema::hasColumn('transactions', 'installment_number');
    }

    private function resetTxForm(): void
    {
        $this->reset([
            'payment_source',
            'bank_account_id',
            'credit_card_id',
            'amount',
            'date',
            'description',
            'status',
        ]);

        $this->payment_source = 'bank';
        $this->status = 'pago';
        $this->date = Carbon::today()->toDateString();
    }

    private function resetTransferForm(): void
    {
        $this->reset([
            'from_bank_account_id',
            'transfer_amount',
            'transfer_date',
            'transfer_description',
            'transfer_status',
        ]);

        $this->transfer_status = 'pago';
        $this->transfer_date = Carbon::today()->toDateString();
    }

    private function normalizeMoney(?string $value): string
    {
        if ($value === null) {
            return '0';
        }

        $clean = preg_replace('/[^\d,\.]/', '', $value);
        if ($clean === null) {
            return '0';
        }

        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return $clean;
    }

    private function userId(): int
    {
        $id = Auth::id();
        if (! $id) {
            abort(401);
        }

        return (int) $id;
    }
}
