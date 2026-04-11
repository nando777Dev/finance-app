<?php

namespace App\Livewire\App\Transactions;

use App\Repositories\Contracts\BankAccountRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CreditCardRepositoryInterface;
use App\Repositories\Contracts\GoalRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
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

    public bool $viewModalOpen = false;

    public ?int $editingId = null;

    public ?int $category_id = null;

    public ?int $goal_id = null;

    public string $payment_source = 'bank';

    public ?int $bank_account_id = null;

    public ?int $credit_card_id = null;

    public string $description = '';

    public string $amount = '';

    public string $date = '';

    public string $type = 'debito';

    public string $status = 'pago';

    public ?string $observations = null;

    public string $installment_mode = 'single';

    public int $installments = 1;

    public string $installment_interval = 'monthly';

    public ?int $interval_days = null;

    public array $viewing = [];

    private TransactionRepositoryInterface $transactions;

    private CategoryRepositoryInterface $categoriesRepo;

    private BankAccountRepositoryInterface $accountsRepo;

    private CreditCardRepositoryInterface $cardsRepo;

    private GoalRepositoryInterface $goalsRepo;

    public function boot(
        TransactionRepositoryInterface $transactions,
        CategoryRepositoryInterface $categories,
        BankAccountRepositoryInterface $accounts,
        CreditCardRepositoryInterface $cards,
        GoalRepositoryInterface $goals
    ): void {
        $this->transactions = $transactions;
        $this->categoriesRepo = $categories;
        $this->accountsRepo = $accounts;
        $this->cardsRepo = $cards;
        $this->goalsRepo = $goals;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->type = 'debito';
        $this->status = 'pago';
        $this->payment_source = 'bank';
        $this->installment_mode = 'single';
        $this->installments = 1;
        $this->installment_interval = 'monthly';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $t = $this->transactions->findOrFail($id);
        if ((int) $t->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = (int) $t->getAttribute('id');
        $this->category_id = $t->getAttribute('category_id') ? (int) $t->getAttribute('category_id') : null;
        $this->goal_id = $t->getAttribute('goal_id') ? (int) $t->getAttribute('goal_id') : null;
        $this->bank_account_id = $t->getAttribute('bank_account_id') ? (int) $t->getAttribute('bank_account_id') : null;
        $this->credit_card_id = $t->getAttribute('credit_card_id') ? (int) $t->getAttribute('credit_card_id') : null;
        $this->payment_source = $this->credit_card_id ? 'card' : 'bank';
        if ($this->credit_card_id && Schema::hasTable('credit_cards') && Schema::hasColumn('credit_cards', 'limit_type') && Schema::hasColumn('credit_cards', 'limit_amount')) {
            $card = $this->cardsRepo->findOrFail($this->credit_card_id);
            $isBenefit = $card->getAttribute('type') === 'debito'
                && in_array($card->getAttribute('limit_type'), ['total', 'mensal'], true)
                && $card->getAttribute('limit_amount') !== null;

            if ($isBenefit) {
                $this->payment_source = 'benefit';
            }
        }
        $this->description = (string) $t->getAttribute('description');
        $this->amount = (string) $t->getAttribute('amount');
        $this->date = (string) optional($t->getAttribute('date'))->format('Y-m-d');
        $this->type = (string) $t->getAttribute('type');
        $this->status = (string) $t->getAttribute('status');
        $this->observations = $t->getAttribute('observations');
        $this->installment_mode = ($t->getAttribute('installment_total') && (int) $t->getAttribute('installment_total') > 1) ? 'installment' : 'single';
        $this->installments = $t->getAttribute('installment_total') ? (int) $t->getAttribute('installment_total') : 1;
        $this->installment_interval = (string) ($t->getAttribute('installment_interval') ?? 'monthly');
        $this->modalOpen = true;
    }

    public function openView(int $id): void
    {
        $with = ['category'];
        if (Schema::hasColumn('transactions', 'goal_id')) {
            $with[] = 'goal';
        }
        if (Schema::hasColumn('transactions', 'bank_account_id')) {
            $with[] = 'bankAccount';
        }
        if (Schema::hasColumn('transactions', 'credit_card_id')) {
            $with[] = 'creditCard';
        }

        $t = $this->transactions
            ->queryByUser($this->userId())
            ->with($with)
            ->findOrFail($id);
        if ((int) $t->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->viewing = [
            'id' => (int) $t->id,
            'description' => (string) $t->description,
            'amount' => (float) $t->amount,
            'date' => optional($t->date)->format('d/m/Y'),
            'type' => (string) $t->type,
            'status' => (string) $t->status,
            'observations' => $t->observations,
            'category' => $t->category ? [
                'id' => (int) $t->category->id,
                'name' => (string) $t->category->name,
                'color' => $t->category->color,
            ] : null,
            'goal' => $t->goal ? [
                'id' => (int) $t->goal->id,
                'name' => (string) $t->goal->name,
            ] : null,
            'bank_account' => $t->bankAccount ? [
                'id' => (int) $t->bankAccount->id,
                'name' => (string) $t->bankAccount->name,
            ] : null,
            'credit_card' => $t->creditCard ? [
                'id' => (int) $t->creditCard->id,
                'name' => (string) $t->creditCard->name,
                'type' => (string) $t->creditCard->type,
                'last4' => $t->creditCard->last4,
            ] : null,
            'installment_label' => $this->installmentLabel($t),
        ];

        $this->viewModalOpen = true;
    }

    public function save(): void
    {
        $userId = $this->userId();

        $hasBankAccountId = Schema::hasColumn('transactions', 'bank_account_id');
        $hasCreditCardId = Schema::hasColumn('transactions', 'credit_card_id');
        $hasGoalId = Schema::hasColumn('transactions', 'goal_id') && Schema::hasTable('goals');

        $this->amount = $this->normalizeMoney($this->amount);

        $validated = $this->validate([
            'category_id' => 'nullable|exists:categories,id',
            'goal_id' => $hasGoalId
                ? ['nullable', Rule::exists('goals', 'id')->where(fn ($q) => $q->where('user_id', $userId))]
                : ['nullable'],
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
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'type' => 'required|in:credito,debito',
            'status' => 'required|in:pago,pendente',
            'observations' => 'nullable|string',
            'installment_mode' => 'required|in:single,installment',
            'installments' => 'required|integer|min:1|max:360',
            'installment_interval' => 'required|in:monthly,yearly,weekly,biweekly,custom',
            'interval_days' => 'nullable|required_if:installment_interval,custom|integer|min:1|max:365',
        ]);

        if (! $hasGoalId) {
            unset($validated['goal_id']);
        }

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

        if ($validated['installment_mode'] === 'single') {
            $validated['installments'] = 1;
        }

        if ($this->editingId) {
            $t = $this->transactions->findOrFail($this->editingId);
            if ((int) $t->getAttribute('user_id') !== $this->userId()) {
                abort(403);
            }

            $update = [
                'category_id' => $validated['category_id'] ?? null,
                'goal_id' => $validated['goal_id'] ?? null,
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'credit_card_id' => $validated['credit_card_id'] ?? null,
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'type' => $validated['type'],
                'status' => $validated['status'],
                'observations' => $validated['observations'] ?? null,
            ];

            if (Schema::hasColumn('transactions', 'installment_interval')) {
                $update['installment_interval'] = $validated['installment_mode'] === 'installment' ? $validated['installment_interval'] : null;
            }

            $this->transactions->update($this->editingId, $update);
        } else {
            if ($validated['installment_mode'] === 'installment' && $validated['installments'] > 1) {
                $payload = [
                    'category_id' => $validated['category_id'] ?? null,
                    'goal_id' => $validated['goal_id'] ?? null,
                    'bank_account_id' => $validated['bank_account_id'] ?? null,
                    'credit_card_id' => $validated['credit_card_id'] ?? null,
                    'description' => $validated['description'],
                    'total_amount' => $validated['amount'],
                    'first_date' => $validated['date'],
                    'installments' => $validated['installments'],
                    'installment_interval' => $validated['installment_interval'],
                    'interval_days' => $validated['installment_interval'] === 'custom' ? $validated['interval_days'] : null,
                    'type' => $validated['type'],
                    'status' => $validated['status'],
                    'observations' => $validated['observations'] ?? null,
                    'is_credit_card' => (bool) ($validated['credit_card_id'] ?? false),
                ];

                if (! Schema::hasColumn('transactions', 'parent_id')) {
                    abort(500);
                }

                $this->transactions->createInstallmentParent($userId, $payload);
            } else {
                $create = [
                    'user_id' => $userId,
                    'category_id' => $validated['category_id'] ?? null,
                    'goal_id' => $validated['goal_id'] ?? null,
                    'bank_account_id' => $validated['bank_account_id'] ?? null,
                    'credit_card_id' => $validated['credit_card_id'] ?? null,
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'date' => $validated['date'],
                    'type' => $validated['type'],
                    'status' => $validated['status'],
                    'observations' => $validated['observations'] ?? null,
                ];

                $this->transactions->create($create);
            }
        }

        $this->modalOpen = false;
        $this->resetForm();
    }

    public function askDelete(int $id): void
    {
        $t = $this->transactions->findOrFail($id);
        if ((int) $t->getAttribute('user_id') !== $this->userId()) {
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

        $t = $this->transactions->findOrFail($this->editingId);
        if ((int) $t->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }
        $this->transactions->delete($this->editingId);
        $this->editingId = null;
        $this->confirmDeleteOpen = false;
    }

    public function render()
    {
        $with = ['category'];
        $hasGoalId = Schema::hasColumn('transactions', 'goal_id');
        $hasBankAccountId = Schema::hasColumn('transactions', 'bank_account_id');
        $hasCreditCardId = Schema::hasColumn('transactions', 'credit_card_id');
        if ($hasGoalId) {
            $with[] = 'goal';
        }
        if ($hasBankAccountId) {
            $with[] = 'bankAccount';
        }
        if ($hasCreditCardId) {
            $with[] = 'creditCard';
        }

        $query = $this->transactions->queryByUser($this->userId())
            ->with($with)
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('description', 'like', '%'.$this->search.'%')
                        ->orWhere('amount', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('id');

        $transactions = $query->paginate($this->perPage);
        $categories = $this->categoriesRepo->query()
            ->where('user_id', $this->userId())
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        $goals = $hasGoalId && Schema::hasTable('goals')
            ? $this->goalsRepo->query()->where('user_id', $this->userId())->orderBy('name')->get(['id', 'name'])
            : collect();

        $accounts = Schema::hasTable('bank_accounts')
            ? $this->accountsRepo->query()->where('user_id', $this->userId())->orderBy('name')->get(['id', 'name'])
            : collect();

        $cards = Schema::hasTable('credit_cards')
            ? $this->cardsRepo->query()->where('user_id', $this->userId())->orderBy('name')->get(['id', 'name', 'type', 'last4', 'limit_type', 'limit_amount'])
            : collect();

        $benefitCards = $cards
            ->filter(fn ($c) => $c->type === 'debito' && in_array($c->limit_type, ['total', 'mensal'], true) && $c->limit_amount !== null)
            ->values();

        return view('livewire.app.transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'goals' => $goals,
            'accounts' => $accounts,
            'cards' => $cards,
            'benefitCards' => $benefitCards,
            'hasGoalId' => $hasGoalId,
            'hasBankAccountId' => $hasBankAccountId,
            'hasCreditCardId' => $hasCreditCardId,
            'hasInstallmentsColumns' => Schema::hasColumn('transactions', 'installment_number') && Schema::hasColumn('transactions', 'installment_total'),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'category_id',
            'goal_id',
            'payment_source',
            'bank_account_id',
            'credit_card_id',
            'description',
            'amount',
            'date',
            'type',
            'status',
            'observations',
            'installment_mode',
            'installments',
            'installment_interval',
            'interval_days',
        ]);
    }

    private function userId(): int
    {
        $id = Auth::id();
        if (! $id) {
            abort(401);
        }

        return (int) $id;
    }

    private function installmentLabel($t): ?string
    {
        if (! Schema::hasColumn('transactions', 'installment_number') || ! Schema::hasColumn('transactions', 'installment_total')) {
            return null;
        }

        $n = $t->getAttribute('installment_number');
        $total = $t->getAttribute('installment_total');

        if (! $n || ! $total) {
            return null;
        }

        return ((int) $n).'/'.((int) $total);
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
}
