<?php

namespace App\Livewire\App\Admin\Users;

use App\Models\User;
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

    public string $tab = 'pending';

    public int $perPage = 15;

    public bool $editModalOpen = false;

    public bool $confirmDeleteOpen = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        if (! Auth::user()?->is_admin) {
            abort(403);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $user = User::query()->findOrFail($id);
        $this->editingId = $user->id;
        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->editModalOpen = true;
    }

    public function save(): void
    {
        if (! $this->editingId) {
            $this->editModalOpen = false;

            return;
        }

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
        ]);

        User::query()->whereKey($this->editingId)->update($validated);
        $this->editModalOpen = false;
        $this->resetEditForm();
    }

    public function approve(int $id): void
    {
        if (! Schema::hasColumn('users', 'approved_at')) {
            abort(500);
        }

        $u = User::query()->findOrFail($id);
        if ($u->is_admin) {
            return;
        }

        $u->approved_at = now();
        $u->is_active = true;
        $u->save();
    }

    public function activate(int $id): void
    {
        $u = User::query()->findOrFail($id);
        if ($u->is_admin) {
            return;
        }
        $u->is_active = true;
        $u->save();
    }

    public function deactivate(int $id): void
    {
        $u = User::query()->findOrFail($id);
        if ($u->is_admin) {
            return;
        }
        $u->is_active = false;
        $u->save();
    }

    public function askDelete(int $id): void
    {
        $this->editingId = $id;
        $this->confirmDeleteOpen = true;
    }

    public function delete(): void
    {
        if (! $this->editingId) {
            $this->confirmDeleteOpen = false;

            return;
        }

        $u = User::query()->findOrFail($this->editingId);
        if ($u->is_admin) {
            $this->confirmDeleteOpen = false;
            $this->editingId = null;

            return;
        }

        $u->tokens()?->delete();
        $u->delete();

        $this->confirmDeleteOpen = false;
        $this->editingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $hasApproval = Schema::hasColumn('users', 'approved_at');

        $base = User::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('created_at');

        $pendingCount = $hasApproval
            ? (clone $base)->whereNull('approved_at')->where('is_admin', false)->count()
            : 0;

        $inactiveCount = (clone $base)->where('is_active', false)->where('is_admin', false)->count();
        $activeCount = (clone $base)->where('is_active', true)->where('is_admin', false)->count();

        $users = match ($this->tab) {
            'pending' => $hasApproval
                ? (clone $base)->whereNull('approved_at')->where('is_admin', false)->paginate($this->perPage)
                : (clone $base)->whereRaw('1 = 0')->paginate($this->perPage),
            'inactive' => (clone $base)->where('is_active', false)->where('is_admin', false)->paginate($this->perPage),
            default => (clone $base)->where('is_admin', false)->paginate($this->perPage),
        };

        $recent = (clone $base)->limit(5)->get(['id', 'name', 'email', 'created_at', 'is_active', 'is_admin', 'approved_at']);

        return view('livewire.app.admin.users.index', [
            'users' => $users,
            'recent' => $recent,
            'hasApproval' => $hasApproval,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ]);
    }

    private function resetEditForm(): void
    {
        $this->reset(['editingId', 'name', 'email']);
    }
}
