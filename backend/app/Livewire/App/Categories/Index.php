<?php

namespace App\Livewire\App\Categories;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
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

    public string $type = 'despesa';

    public ?string $color = null;

    public ?string $icon = null;

    private CategoryRepositoryInterface $categories;

    public function boot(CategoryRepositoryInterface $categories): void
    {
        $this->categories = $categories;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'type', 'color', 'icon']);
        $this->type = 'despesa';
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $category = $this->categories->findOrFail($id);
        if ((int) $category->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->editingId = $category->getAttribute('id');
        $this->name = (string) $category->getAttribute('name');
        $this->type = (string) $category->getAttribute('type');
        $this->color = $category->getAttribute('color');
        $this->icon = $category->getAttribute('icon');
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:receita,despesa',
            'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            $category = $this->categories->findOrFail($this->editingId);
            if ((int) $category->getAttribute('user_id') !== $this->userId()) {
                abort(403);
            }

            $this->categories->update($this->editingId, $validated);
        } else {
            $this->categories->create(array_merge($validated, [
                'user_id' => $this->userId(),
            ]));
        }

        $this->modalOpen = false;
        $this->reset(['editingId', 'name', 'type', 'color', 'icon']);
        $this->type = 'despesa';
    }

    public function askDelete(int $id): void
    {
        $category = $this->categories->findOrFail($id);
        if ((int) $category->getAttribute('user_id') !== $this->userId()) {
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

        $category = $this->categories->findOrFail($this->editingId);
        if ((int) $category->getAttribute('user_id') !== $this->userId()) {
            abort(403);
        }

        $this->categories->delete($this->editingId);
        $this->confirmDeleteOpen = false;
        $this->editingId = null;
    }

    public function render()
    {
        $query = $this->categories->query()
            ->where('user_id', $this->userId())
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('created_at');

        $categories = $query->paginate($this->perPage);

        return view('livewire.app.categories.index', [
            'categories' => $categories,
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
