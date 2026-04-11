<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm text-slate-500">Metas</div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $goal->name }}</h1>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <button
                type="button"
                wire:click="openDeposit"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-500"
            >
                Adicionar valor
            </button>
            <button
                type="button"
                wire:click="openWithdraw"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-semibold text-white hover:bg-red-500"
            >
                Retirar
            </button>
            <button
                type="button"
                wire:click="openTransfer"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
            >
                Transferir da conta
            </button>
        </div>
    </div>

    @php
        $pct = max(0, min(100, (float) $percent));
        $near = $pct >= 80 && $pct < 100;
        $gradient = 'conic-gradient(#4f46e5 0% '.$pct.'%, #e2e8f0 '.$pct.'% 100%)';
    @endphp

    @if ($near)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            Falta pouco para concluir sua meta.
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Progresso</div>
                    <div class="text-xs text-slate-500">Acompanhamento da meta</div>
                </div>
            </div>

            <div class="mt-5 grid gap-6 md:grid-cols-2">
                <div class="flex items-center justify-center">
                    <div class="relative h-44 w-44 rounded-full" @style(['background-image: '.$gradient])>
                        <div class="absolute inset-6 rounded-full bg-white"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-semibold text-slate-900">{{ number_format($pct, 0) }}%</div>
                                <div class="text-xs text-slate-500">concluído</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-600">Guardado</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">R$ {{ number_format($saved, 2, ',', '.') }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-600">Falta</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">R$ {{ number_format($remaining, 2, ',', '.') }}</div>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold text-slate-600">Objetivo</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">R$ {{ number_format((float) $goal->target_amount, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Evolução (6 meses)</div>
            <div class="mt-1 text-xs text-slate-500">Entradas x retiradas</div>

            @php
                $max = 0;
                foreach ($monthlyTrend as $p) {
                    $max = max($max, $p['income'] ?? 0, $p['expense'] ?? 0);
                }
            @endphp

            <div class="mt-5 grid grid-cols-6 items-end gap-3">
                @foreach ($monthlyTrend as $p)
                    @php
                        $incomePct = $max > 0 ? (int) round((($p['income'] ?? 0) / $max) * 100) : 0;
                        $expensePct = $max > 0 ? (int) round((($p['expense'] ?? 0) / $max) * 100) : 0;
                        $incomePct = max(0, min(100, $incomePct));
                        $expensePct = max(0, min(100, $expensePct));
                    @endphp
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-28 w-full rounded-xl bg-slate-50 p-2">
                            <div class="flex h-full items-end gap-2">
                                <div class="w-1/2 rounded-lg bg-gradient-to-t from-emerald-500 to-emerald-700" @style(['height: '.$incomePct.'%'])></div>
                                <div class="w-1/2 rounded-lg bg-gradient-to-t from-red-500 to-red-700" @style(['height: '.$expensePct.'%'])></div>
                            </div>
                        </div>
                        <div class="text-[11px] font-semibold text-slate-500">{{ $p['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-sm font-semibold text-slate-900">Movimentações da meta</div>
        <div class="mt-1 text-xs text-slate-500">Últimos lançamentos vinculados</div>

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-7">Descrição</div>
                <div class="col-span-2">Tipo</div>
                <div class="col-span-3 text-right">Valor</div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recent as $t)
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-7 min-w-0">
                            <div class="truncate font-medium text-slate-900">{{ $t['description'] }}</div>
                            <div class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($t['date'])->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-span-2">
                            @if ($t['type'] === 'debito')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Retirada</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Entrada</span>
                            @endif
                        </div>
                        <div class="col-span-3 text-right font-semibold text-slate-900">
                            R$ {{ number_format((float) $t['amount'], 2, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhuma movimentação ainda.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if ($txModalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('txModalOpen', false)"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">{{ $tx_type === 'credito' ? 'Adicionar valor' : 'Retirar valor' }}</div>
                        <div class="text-sm text-slate-500">Movimente o saldo desta meta.</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-slate-50"
                        wire:click="$set('txModalOpen', false)"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Origem</label>
                                <select
                                    wire:model.live="payment_source"
                                    class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                >
                                    <option value="bank">Conta bancária</option>
                                    <option value="card">Cartão</option>
                                    <option value="benefit">Benefício</option>
                                    <option value="cash">Dinheiro</option>
                                </select>
                                @error('payment_source') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                @if ($payment_source === 'cash')
                                    <label class="text-xs font-semibold text-slate-600">Dinheiro</label>
                                    <div class="mt-1 flex h-10 items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700">
                                        Movimento em dinheiro
                                    </div>
                                @elseif ($payment_source === 'benefit')
                                    <label class="text-xs font-semibold text-slate-600">Benefício</label>
                                    <select
                                        wire:model.defer="credit_card_id"
                                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                    >
                                        <option value="">Selecione</option>
                                        @foreach ($benefitCards as $c)
                                            <option value="{{ $c['id'] }}">{{ $c['name'] }}{{ ! empty($c['last4']) ? ' (**** '.$c['last4'].')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('credit_card_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                @elseif ($payment_source === 'card')
                                    <label class="text-xs font-semibold text-slate-600">Cartão</label>
                                    <select
                                        wire:model.defer="credit_card_id"
                                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                    >
                                        <option value="">Selecione</option>
                                        @foreach ($cards as $c)
                                            <option value="{{ $c['id'] }}">{{ $c['name'] }}{{ ! empty($c['last4']) ? ' (**** '.$c['last4'].')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('credit_card_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                @else
                                    <label class="text-xs font-semibold text-slate-600">Conta bancária</label>
                                    <select
                                        wire:model.defer="bank_account_id"
                                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                    >
                                        <option value="">Selecione</option>
                                        @foreach ($accounts as $a)
                                            <option value="{{ $a['id'] }}">{{ $a['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_account_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Descrição</label>
                        <input
                            type="text"
                            wire:model.defer="description"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Data</label>
                        <input
                            type="date"
                            wire:model.defer="date"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('date') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Valor</label>
                        <input
                            type="text"
                            wire:model.defer="amount"
                            data-money
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('amount') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Status</label>
                        <select
                            wire:model.defer="status"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="pago">Pago</option>
                            <option value="pendente">Pendente</option>
                        </select>
                        @error('status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="$set('txModalOpen', false)"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="saveTx"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                    >
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($transferModalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('transferModalOpen', false)"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">Transferir para a meta</div>
                        <div class="text-sm text-slate-500">Move saldo da conta para a meta sem contar como receita/despesa.</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-slate-50"
                        wire:click="$set('transferModalOpen', false)"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Conta de origem</label>
                        <select
                            wire:model.defer="from_bank_account_id"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="">Selecione</option>
                            @foreach ($accounts as $a)
                                <option value="{{ $a['id'] }}">{{ $a['name'] }}</option>
                            @endforeach
                        </select>
                        @error('from_bank_account_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Descrição</label>
                        <input
                            type="text"
                            wire:model.defer="transfer_description"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            placeholder="Ex.: Transferência para a meta"
                        />
                        @error('transfer_description') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Data</label>
                        <input
                            type="date"
                            wire:model.defer="transfer_date"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('transfer_date') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Valor</label>
                        <input
                            type="text"
                            wire:model.defer="transfer_amount"
                            data-money
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        @error('transfer_amount') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Status</label>
                        <select
                            wire:model.defer="transfer_status"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="pago">Pago</option>
                            <option value="pendente">Pendente</option>
                        </select>
                        @error('transfer_status') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="$set('transferModalOpen', false)"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        wire:click="saveTransfer"
                        class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                    >
                        Transferir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
