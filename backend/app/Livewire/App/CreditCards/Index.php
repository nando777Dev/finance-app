<?php

namespace App\Livewire\App\CreditCards;

use App\Repositories\Contracts\CreditCardRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public ?int $editingId = null;

    public string $name = '';

    public string $type = 'credito';

    public ?string $brand = null;

    public ?string $last4 = null;

    public ?string $limit_amount = null;

    public string $limit_type = 'sem_limite';

    public ?int $due_day = null;

    public ?string $monthly_limit = null;

    private CreditCardRepositoryInterface $cards;

    public function boot(CreditCardRepositoryInterface $cards): void
    {
        $this->cards = $cards;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->type = 'credito';
        $this->limit_type = 'sem_limite';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $card = $this->cards->findOrFail($id);
        if ((int) $card->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = (int) $card->getAttribute('id');
        $this->name = (string) $card->getAttribute('name');
        $this->type = (string) $card->getAttribute('type');
        $this->brand = $card->getAttribute('brand');
        $this->last4 = $card->getAttribute('last4');
        $this->limit_amount = $card->getAttribute('limit_amount') !== null ? (string) $card->getAttribute('limit_amount') : null;
        $this->limit_type = (string) $card->getAttribute('limit_type');
        $this->due_day = $card->getAttribute('due_day') !== null ? (int) $card->getAttribute('due_day') : null;
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:credito,debito',
            'brand' => 'nullable|string|max:255',
            'last4' => 'nullable|digits:4',
            'limit_amount' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $this->limit_type !== 'sem_limite'),
            ],
            'limit_type' => 'required|in:total,mensal,sem_limite',
            'due_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
                Rule::requiredIf(fn () => $this->type === 'credito'),
            ],
            'monthly_limit' => 'nullable|numeric|min:0',
        ]);

        if (($validated['type'] ?? 'credito') === 'debito') {
            $validated['due_day'] = null;
        }

        if (($validated['limit_type'] ?? 'sem_limite') === 'sem_limite') {
            $validated['limit_amount'] = null;
        }

        if ($this->editingId) {
            $card = $this->cards->findOrFail($this->editingId);
            if ((int) $card->getAttribute('user_id') !== $this->userId()) {
                abort(403);
            }

            $this->cards->update($this->editingId, $validated);
        } else {
            $this->cards->create(array_merge($validated, [
                'user_id' => $this->userId(),
            ]));
        }

        if ($this->monthly_limit !== null && $this->type === 'debito') {
            $id = $this->editingId ?? $this->cards->query()->where('user_id', $this->userId())->orderByDesc('id')->value('id');
            if ($id) {
                $now = now();
                DB::table('benefit_monthly_limits')
                    ->updateOrInsert(
                        ['credit_card_id' => $id, 'year' => $now->year, 'month' => $now->month],
                        ['amount' => (float) $this->monthly_limit, 'updated_at' => now(), 'created_at' => now()]
                    );
            }
        }

        $this->modalOpen = false;
        $this->resetForm();
        $this->type = 'credito';
        $this->limit_type = 'sem_limite';
    }

    public function askDelete(int $id): void
    {
        $card = $this->cards->findOrFail($id);
        if ((int) $card->getAttribute('user_id') !== $this->userId()) {
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

        $card = $this->cards->findOrFail($this->editingId);
        if ((int) $card->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->cards->delete($this->editingId);
        $this->confirmDeleteOpen = false;
        $this->editingId = null;
    }

    public function render()
    {
        $query = $this->cards->query()
            ->where('user_id', $this->userId())
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('brand', 'like', '%'.$this->search.'%')
                        ->orWhere('last4', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name');

        $cards = $query->paginate($this->perPage);

        return view('livewire.app.credit-cards.index', [
            'cards' => $cards,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'type',
            'brand',
            'last4',
            'limit_amount',
            'limit_type',
            'due_day',
            'monthly_limit',
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
}
