<?php

namespace App\Livewire\App\Goals;

use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Repositories\Contracts\CreditCardRepositoryInterface;
use App\Repositories\Contracts\GoalRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public bool $modalOpen = false;

    public bool $confirmDeleteOpen = false;

    public bool $txModalOpen = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $target_amount = '';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public string $status = 'ativa';

    public ?int $txGoalId = null;

    public string $txGoalName = '';

    public string $tx_type = 'credito';

    public string $payment_source = 'bank';

    public ?int $bank_account_id = null;

    public ?int $credit_card_id = null;

    public string $amount = '';

    public string $date = '';

    public string $description = '';

    public string $tx_status = 'pago';

    private GoalRepositoryInterface $goals;

    private TransactionRepositoryInterface $transactions;

    private BankAccountRepositoryInterface $accountsRepo;

    private CreditCardRepositoryInterface $cardsRepo;

    public function boot(
        GoalRepositoryInterface $goals,
        TransactionRepositoryInterface $transactions,
        BankAccountRepositoryInterface $accounts,
        CreditCardRepositoryInterface $cards
    ): void {
        $this->goals = $goals;
        $this->transactions = $transactions;
        $this->accountsRepo = $accounts;
        $this->cardsRepo = $cards;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->status = 'ativa';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $g = $this->goals->findOrFail($id);
        if ((int) $g->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = (int) $g->getAttribute('id');
        $this->name = (string) $g->getAttribute('name');
        $this->target_amount = (string) $g->getAttribute('target_amount');
        $this->start_date = optional($g->getAttribute('start_date'))->format('Y-m-d');
        $this->end_date = optional($g->getAttribute('end_date'))->format('Y-m-d');
        $this->status = (string) $g->getAttribute('status');
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:ativa,pausada,concluida',
        ]);

        $payload = array_merge($validated, [
            'user_id' => $this->userId(),
        ]);

        if ($this->editingId) {
            $g = $this->goals->findOrFail($this->editingId);
            if ((int) $g->getAttribute('user_id') !== $this->userId()) {
                abort(403);
            }

            unset($payload['user_id']);
            $this->goals->update($this->editingId, $payload);
        } else {
            $this->goals->create($payload);
        }

        $this->modalOpen = false;
        $this->resetForm();
    }

    public function askDelete(int $id): void
    {
        $g = $this->goals->findOrFail($id);
        if ((int) $g->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = $id;
        $this->confirmDeleteOpen = true;
    }

    public function delete(): void
    {
        if (! $this->editingId) {
            $this->confirmDeleteOpen = false;

            return;
        }

        $g = $this->goals->findOrFail($this->editingId);
        if ((int) $g->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->goals->delete($this->editingId);
        $this->confirmDeleteOpen = false;
        $this->editingId = null;
    }

    public function openDepositTx(int $goalId): void
    {
        $g = $this->goals->findOrFail($goalId);
        if ((int) $g->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->resetTxForm();
        $this->txGoalId = (int) $g->getAttribute('id');
        $this->txGoalName = (string) $g->getAttribute('name');
        $this->tx_type = 'credito';
        $this->txModalOpen = true;
    }

    public function openWithdrawTx(int $goalId): void
    {
        $g = $this->goals->findOrFail($goalId);
        if ((int) $g->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->resetTxForm();
        $this->txGoalId = (int) $g->getAttribute('id');
        $this->txGoalName = (string) $g->getAttribute('name');
        $this->tx_type = 'debito';
        $this->txModalOpen = true;
    }

    public function saveTx(): void
    {
        if (! Schema::hasColumn('transactions', 'goal_id') || ! Schema::hasTable('goals')) {
            abort(500);
        }

        if (! $this->txGoalId) {
            abort(500);
        }

        $userId = $this->userId();
        $hasBankAccountId = Schema::hasColumn('transactions', 'bank_account_id');
        $hasCreditCardId = Schema::hasColumn('transactions', 'credit_card_id');

        $this->amount = $this->normalizeMoney($this->amount);

        $validated = $this->validate([
            'txGoalId' => [
                'required',
                Rule::exists('goals', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
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
            'tx_status' => 'required|in:pago,pendente',
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

        $payload = [
            'user_id' => $userId,
            'goal_id' => $validated['txGoalId'],
            'category_id' => null,
            'bank_account_id' => $validated['bank_account_id'] ?? null,
            'credit_card_id' => $validated['credit_card_id'] ?? null,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'type' => $validated['tx_type'],
            'status' => $validated['tx_status'],
            'observations' => null,
        ];

        if (Schema::hasColumn('transactions', 'kind')) {
            $payload['kind'] = 'regular';
        }

        if (Schema::hasColumn('transactions', 'transfer_group')) {
            $payload['transfer_group'] = null;
        }

        $this->transactions->create($payload);

        $this->txModalOpen = false;
        $this->resetTxForm();
    }

    public function render()
    {
        $query = $this->goals->query()
            ->where('user_id', $this->userId())
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('status')
            ->orderBy('name');

        $goals = $query->paginate($this->perPage);
        $progress = $this->goalProgressMap($goals->pluck('id')->all());

        $accounts = Schema::hasTable('bank_accounts')
            ? $this->accountsRepo->query()->where('user_id', $this->userId())->orderBy('name')->get(['id', 'name'])
            : collect();

        $cards = Schema::hasTable('credit_cards')
            ? $this->cardsRepo->query()->where('user_id', $this->userId())->orderBy('name')->get(['id', 'name', 'type', 'last4', 'limit_type', 'limit_amount'])
            : collect();

        $benefitCards = $cards
            ->filter(fn ($c) => $c->type === 'debito' && in_array($c->limit_type, ['total', 'mensal'], true) && $c->limit_amount !== null)
            ->values();

        return view('livewire.app.goals.index', [
            'goals' => $goals,
            'progress' => $progress,
            'hasGoalId' => Schema::hasColumn('transactions', 'goal_id'),
            'accounts' => $accounts,
            'cards' => $cards,
            'benefitCards' => $benefitCards,
        ]);
    }

    private function goalProgressMap(array $goalIds): array
    {
        if (empty($goalIds) || ! Schema::hasColumn('transactions', 'goal_id')) {
            return [];
        }

        $moves = DB::table('transactions')
            ->where('user_id', $this->userId())
            ->whereIn('goal_id', $goalIds)
            ->when($this->excludeInstallmentParentsEnabled(), function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNotNull('parent_id')
                        ->orWhereNull('installment_total');
                });
            })
            ->groupBy('goal_id')
            ->select('goal_id', DB::raw('SUM(CASE WHEN type = "credito" THEN amount ELSE -amount END) as total'))
            ->pluck('total', 'goal_id');

        $map = [];
        foreach ($moves as $goalId => $total) {
            $map[(int) $goalId] = (float) $total;
        }

        return $map;
    }

    private function excludeInstallmentParentsEnabled(): bool
    {
        return Schema::hasColumn('transactions', 'parent_id')
            && Schema::hasColumn('transactions', 'installment_total')
            && Schema::hasColumn('transactions', 'installment_number');
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'target_amount',
            'start_date',
            'end_date',
            'status',
        ]);
    }

    private function resetTxForm(): void
    {
        $this->reset([
            'txGoalId',
            'txGoalName',
            'tx_type',
            'payment_source',
            'bank_account_id',
            'credit_card_id',
            'amount',
            'date',
            'description',
            'tx_status',
        ]);

        $this->payment_source = 'bank';
        $this->tx_status = 'pago';
        $this->date = Carbon::today()->toDateString();
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
