<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500">Cadastros</div>
            <h1 class="text-2xl font-semibold text-slate-900">Contas bancárias</h1>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
        >
            Adicionar conta
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="text-sm font-semibold text-slate-900">Lista</div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar..."
                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none md:w-72"
            />
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-3">Nome</div>
                <div class="col-span-3">Banco</div>
                <div class="col-span-2">Agência</div>
                <div class="col-span-2">Conta</div>
                <div class="col-span-1 text-right">Saldo</div>
                <div class="col-span-1 text-right">Ações</div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($accounts as $acc)
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-3 min-w-0">
                            <div class="truncate font-medium text-slate-900">{{ $acc->name }}</div>
                            <div class="text-xs text-slate-500">{{ $acc->type }}</div>
                        </div>
                        <div class="col-span-3 text-slate-700">{{ $acc->bank_name ?? '-' }}</div>
                        <div class="col-span-2 text-slate-700">{{ $acc->agency_number ?? '-' }}</div>
                        <div class="col-span-2 text-slate-700">{{ $acc->account_number ?? '-' }}</div>
                        <div class="col-span-1 text-right font-semibold text-slate-900">
                            @php
                                $sum = $balances[$acc->id] ?? 0;
                                $current = (float) ($acc->opening_balance ?? 0) + (float) $sum;
                            @endphp
                            R$ {{ number_format($current, 2, ',', '.') }}
                        </div>
                        <div class="col-span-1 flex justify-end">
                            <details class="relative">
                                <summary
                                    class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 hover:bg-slate-50"
                                >
                                    Ações ▾
                                </summary>
                                <div class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $acc->id }})"
                                        class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-900 hover:bg-slate-50"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="askDelete({{ $acc->id }})"
                                        class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            </details>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhuma conta cadastrada.</div>
                @endforelse
            </div>

            <div class="border-t border-slate-100 px-4 py-3">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>

    @if ($modalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('modalOpen', false)"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">{{ $editingId ? 'Editar conta' : 'Nova conta' }}</div>
                        <div class="text-sm text-slate-500">Preencha os campos abaixo.</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-slate-50"
                        wire:click="$set('modalOpen', false)"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Nome</label>
                        <input
                            type="text"
                            wire:model.defer="name"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tipo</label>
                        <input
                            type="text"
                            wire:model.defer="type"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('type') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Banco</label>
                        <input
                            type="text"
                            wire:model.defer="bank_name"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('bank_name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Agência</label>
                        <input
                            type="text"
                            wire:model.defer="agency_number"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('agency_number') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Conta</label>
                        <input
                            type="text"
                            wire:model.defer="account_number"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('account_number') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Saldo inicial</label>
                        <input
                            type="number"
                            step="0.01"
                            wire:model.defer="opening_balance"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('opening_balance') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="$set('modalOpen', false)"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="save"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                    >
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmDeleteOpen)
        <div class="fixed inset-0 z-[70] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('confirmDeleteOpen', false)"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Excluir conta?</div>
                <div class="mt-2 text-sm text-slate-500">Essa ação não pode ser desfeita.</div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="$set('confirmDeleteOpen', false)"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="delete"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-500"
                    >
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
