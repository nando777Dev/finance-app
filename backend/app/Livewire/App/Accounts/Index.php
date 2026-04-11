<?php

namespace App\Livewire\App\Accounts;

use App\Repositories\Contracts\BankAccountRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public ?int $editingId = null;

    public string $name = '';

    public string $type = 'corrente';

    public ?string $bank_name = null;

    public ?string $agency_number = null;

    public ?string $account_number = null;

    private BankAccountRepositoryInterface $accounts;

    public function boot(BankAccountRepositoryInterface $accounts): void
    {
        $this->accounts = $accounts;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->type = 'corrente';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $acc = $this->accounts->findOrFail($id);
        if ((int) $acc->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = (int) $acc->getAttribute('id');
        $this->name = (string) $acc->getAttribute('name');
        $this->type = (string) $acc->getAttribute('type');
        $this->bank_name = $acc->getAttribute('bank_name');
        $this->agency_number = $acc->getAttribute('agency_number');
        $this->account_number = $acc->getAttribute('account_number');
        $this->opening_balance = $acc->getAttribute('opening_balance') !== null ? (string) $acc->getAttribute('opening_balance') : null;
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'agency_number' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
        ]);

        if ($this->editingId) {
            $acc = $this->accounts->findOrFail($this->editingId);
            if ((int) $acc->getAttribute('user_id') !== $this->userId()) {
                abort(403);
            }

            $this->accounts->update($this->editingId, $validated);
        } else {
            $this->accounts->create(array_merge($validated, [
                'user_id' => $this->userId(),
            ]));
        }

        $this->modalOpen = false;
        $this->resetForm();
        $this->type = 'corrente';
    }

    public function askDelete(int $id): void
    {
        $acc = $this->accounts->findOrFail($id);
        if ((int) $acc->getAttribute('user_id') !== $this->userId()) {
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

        $acc = $this->accounts->findOrFail($this->editingId);
        if ((int) $acc->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->accounts->delete($this->editingId);
        $this->confirmDeleteOpen = false;
        $this->editingId = null;
    }

    public function render()
    {
        $query = $this->accounts->query()
            ->where('user_id', $this->userId())
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                        ->orWhere('agency_number', 'like', '%'.$this->search.'%')
                        ->orWhere('account_number', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name');

        $accounts = $query->paginate($this->perPage);

        return view('livewire.app.accounts.index', [
            'accounts' => $accounts,
            'balances' => $this->computeBalances(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'type',
            'bank_name',
            'agency_number',
            'account_number',
            'opening_balance',
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

    private function computeBalances(): array
    {
        $balances = [];

        $base = DB::table('transactions')
            ->selectRaw('bank_account_id, SUM(CASE WHEN type = "credito" THEN amount ELSE -amount END) as total')
            ->where('user_id', $this->userId())
            ->whereNotNull('bank_account_id')
            ->groupBy('bank_account_id')
            ->pluck('total', 'bank_account_id');

        foreach ($base as $accountId => $sum) {
            $balances[(int) $accountId] = (float) $sum;
        }

        return $balances;
    }
}
