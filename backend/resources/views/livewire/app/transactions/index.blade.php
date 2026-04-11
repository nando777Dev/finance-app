<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500">Lançamentos</div>
            <h1 class="text-2xl font-semibold text-slate-900">Transações</h1>
        </div>
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
        >
            Adicionar lançamento
        </button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="text-sm font-semibold text-slate-900">Lista</div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por descrição/valor..."
                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none md:w-72"
            />
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-4">Descrição</div>
                <div class="col-span-2">Categoria</div>
                <div class="col-span-1">Tipo</div>
                <div class="col-span-2">Valor</div>
                <div class="col-span-1 text-right">Data</div>
                <div class="col-span-2 text-right">Ações</div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($transactions as $t)
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-4 min-w-0">
                            <div class="truncate font-medium text-slate-900">
                                {{ $t->description }}
                                @if ($hasInstallmentsColumns && $t->installment_number && $t->installment_total)
                                    <span class="ml-2 rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        {{ $t->installment_number }}/{{ $t->installment_total }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">{{ $t->status }}</div>
                            @if ($hasGoalId && $t->goal)
                                <div class="mt-1 text-xs">
                                    <a href="{{ url('/app/goals/'.$t->goal->id) }}" class="font-semibold text-brand-700 hover:text-brand-600">
                                        Meta: {{ $t->goal->name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="col-span-2">
                            @php
                                $catName = $t->category?->name;
                                $catColor = $t->category?->color;
                                $catColorOk = is_string($catColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $catColor);
                            @endphp
                            <div class="flex items-center gap-2 text-slate-700">
                                @if ($catColorOk)
                                    <span class="h-2.5 w-2.5 rounded-full border border-slate-200" @style(['background-color: '.$catColor])></span>
                                @else
                                    <span class="h-2.5 w-2.5 rounded-full border border-slate-200 bg-slate-100"></span>
                                @endif
                                <span class="truncate">{{ $catName ?? '-' }}</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                @if ($hasCreditCardId && $t->creditCard)
                                    @php
                                        $isBenefit = $t->creditCard->type === 'debito'
                                            && in_array($t->creditCard->limit_type ?? null, ['total', 'mensal'], true)
                                            && ($t->creditCard->limit_amount ?? null) !== null;
                                    @endphp
                                    {{ $isBenefit ? 'Benefício' : 'Cartão' }}: {{ $t->creditCard->name }}{{ $t->creditCard->last4 ? ' (**** '.$t->creditCard->last4.')' : '' }}
                                @elseif ($hasBankAccountId && $t->bankAccount)
                                    Conta: {{ $t->bankAccount->name }}
                                @else
                                    Dinheiro
                                @endif
                            </div>
                        </div>
                        <div class="col-span-1">
                            @if ($t->type === 'debito')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Despesa</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Receita</span>
                            @endif
                        </div>
                        <div class="col-span-2 font-semibold text-slate-900">
                            R$ {{ number_format((float) $t->amount, 2, ',', '.') }}
                        </div>
                        <div class="col-span-1 text-right text-slate-600">
                            {{ optional($t->date)->format('d/m/Y') }}
                        </div>
                        <div class="col-span-2 flex justify-end gap-2">
                            <button
                                type="button"
                                wire:click="openView({{ $t->id }})"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100"
                                title="Visualizar"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                    <path
                                        fill-rule="evenodd"
                                        d="M12 4.5c-4.478 0-8.27 2.943-10.5 7.5 2.23 4.557 6.022 7.5 10.5 7.5s8.27-2.943 10.5-7.5C20.27 7.443 16.478 4.5 12 4.5Zm0 10.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                            <button
                                type="button"
                                wire:click="openEdit({{ $t->id }})"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100"
                                title="Editar"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                    <path
                                        d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.5 1.5 3.712 3.712 1.5-1.5a2.625 2.625 0 0 0 0-3.712Z"
                                    />
                                    <path
                                        d="m18.75 8.25-3.712-3.712L3.75 15.826V19.5h3.674L18.75 8.25Z"
                                    />
                                </svg>
                            </button>
                            <button
                                type="button"
                                wire:click="askDelete({{ $t->id }})"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-700 hover:bg-red-100"
                                title="Excluir"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                    <path
                                        fill-rule="evenodd"
                                        d="M9 3.75A2.25 2.25 0 0 0 6.75 6v.75H4.5a.75.75 0 0 0 0 1.5h.75v11.25A2.25 2.25 0 0 0 7.5 21.75h9A2.25 2.25 0 0 0 18.75 19.5V8.25h.75a.75.75 0 0 0 0-1.5h-2.25V6A2.25 2.25 0 0 0 15 3.75H9Zm6.75 3V6A.75.75 0 0 0 15 5.25H9A.75.75 0 0 0 8.25 6v.75h7.5Zm-6 4.5a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-6a.75.75 0 0 1 .75-.75Zm4.5.75a.75.75 0 0 0-1.5 0v6a.75.75 0 0 0 1.5 0v-6Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhum lançamento encontrado.</div>
                @endforelse
            </div>

            <div class="border-t border-slate-100 px-4 py-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    @if ($modalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('modalOpen', false)"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">
                            {{ $editingId ? 'Editar lançamento' : 'Novo lançamento' }}
                        </div>
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
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tipo</label>
                        <select
                            wire:model.defer="type"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="debito">Despesa</option>
                            <option value="credito">Receita</option>
                        </select>
                        @error('type') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Origem</label>
                                <select
                                    wire:model.live="payment_source"
                                    class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                >
                                    @if ($hasBankAccountId)
                                        <option value="bank">Conta bancária</option>
                                    @endif
                                    @if ($hasCreditCardId)
                                        <option value="card">Cartão</option>
                                        <option value="benefit">Benefício</option>
                                    @endif
                                    <option value="cash">Dinheiro</option>
                                </select>
                                @error('payment_source') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                            </div>

                            <div>
                                @if ($payment_source === 'cash')
                                    <label class="text-xs font-semibold text-slate-600">Dinheiro</label>
                                    <div class="mt-1 flex h-10 items-center rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700">
                                        Lançamento em dinheiro
                                    </div>
                                @elseif ($payment_source === 'benefit')
                                    <label class="text-xs font-semibold text-slate-600">Benefício</label>
                                    <select
                                        wire:model.defer="credit_card_id"
                                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                    >
                                        <option value="">Selecione</option>
                                        @foreach ($benefitCards as $card)
                                            <option value="{{ $card->id }}">
                                                {{ $card->name }}{{ $card->last4 ? ' (**** '.$card->last4.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('credit_card_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                                    @if ($benefitCards->isEmpty())
                                        <div class="mt-2 text-xs text-slate-500">Cadastre um cartão débito com limite mensal/total para usar como benefício.</div>
                                    @endif
                                @elseif ($payment_source === 'card')
                                    <label class="text-xs font-semibold text-slate-600">Cartão</label>
                                    <select
                                        wire:model.defer="credit_card_id"
                                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                                    >
                                        <option value="">Selecione</option>
                                        @foreach ($cards as $card)
                                            <option value="{{ $card->id }}">
                                                {{ $card->name }}{{ $card->last4 ? ' (**** '.$card->last4.')' : '' }} - {{ $card->type }}
                                            </option>
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
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
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
                        <label class="text-xs font-semibold text-slate-600">Categoria</label>
                        <select
                            wire:model.defer="category_id"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="">Selecione</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @php
                            $selectedId = $category_id ? (int) $category_id : null;
                            $selected = $selectedId ? $categories->firstWhere('id', $selectedId) : null;
                            $selColor = $selected?->color;
                            $selColorOk = is_string($selColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $selColor);
                        @endphp
                        @if ($selected)
                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                                @if ($selColorOk)
                                    <span class="h-2.5 w-2.5 rounded-full border border-slate-200" @style(['background-color: '.$selColor])></span>
                                @else
                                    <span class="h-2.5 w-2.5 rounded-full border border-slate-200 bg-slate-100"></span>
                                @endif
                                <span>{{ $selected->name }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Meta</label>
                        <select
                            wire:model.defer="goal_id"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            @disabled(! $hasGoalId)
                        >
                            <option value="">Sem meta</option>
                            @foreach ($goals as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        @error('goal_id') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if (! $hasGoalId)
                            <div class="mt-1 text-[11px] font-semibold text-slate-500">Ative esse recurso rodando as migrations.</div>
                        @endif
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
                        <label class="text-xs font-semibold text-slate-600">Modo</label>
                        <select
                            wire:model.live="installment_mode"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        >
                            <option value="single">Parcela única</option>
                            <option value="installment">Parcelado</option>
                        </select>
                        @error('installment_mode') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Intervalo</label>
                        <select
                            wire:model.defer="installment_interval"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm focus:border-brand-600 focus:outline-none {{ $installment_mode === 'single' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-900' }}"
                            @disabled($installment_mode === 'single')
                        >
                            <option value="monthly">Mensal</option>
                            <option value="yearly">Anual</option>
                            <option value="weekly">Semanal</option>
                            <option value="biweekly">Quinzenal</option>
                            <option value="custom">Personalizado (dias)</option>
                        </select>
                        @error('installment_interval') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        @if ($installment_mode === 'single')
                            <div class="mt-1 text-[11px] font-semibold text-slate-500">Bloqueado em “Parcela única”.</div>
                        @endif
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Número de parcelas</label>
                        <input
                            type="number"
                            min="1"
                            max="360"
                            wire:model.defer="installments"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            @disabled($installment_mode === 'single')
                        />
                        @error('installments') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                    </div>

                    @if ($installment_interval === 'custom' && $installment_mode === 'installment')
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Dias entre parcelas</label>
                            <input
                                type="number"
                                min="1"
                                max="365"
                                wire:model.defer="interval_days"
                                class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            />
                            @error('interval_days') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Valor</label>
                        <input
                            type="number"
                            step="0.01"
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

                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Observações</label>
                        <textarea
                            wire:model.defer="observations"
                            rows="3"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        ></textarea>
                        @error('observations') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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

    @if ($viewModalOpen)
        <div class="fixed inset-0 z-[65] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('viewModalOpen', false)"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">Detalhes do lançamento</div>
                        <div class="mt-1 text-sm text-slate-500">Informações completas</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-slate-50"
                        wire:click="$set('viewModalOpen', false)"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <div class="text-xs font-semibold text-slate-600">Descrição</div>
                        <div class="mt-1 text-sm font-medium text-slate-900">{{ $viewing['description'] ?? '-' }}</div>
                        @if (! empty($viewing['installment_label']))
                            <div class="mt-2">
                                <span class="rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $viewing['installment_label'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Categoria</div>
                        @php
                            $vCat = $viewing['category'] ?? null;
                            $vColor = is_array($vCat) ? ($vCat['color'] ?? null) : null;
                            $vColorOk = is_string($vColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $vColor);
                        @endphp
                        <div class="mt-1 flex items-center gap-2 text-sm text-slate-900">
                            @if ($vColorOk)
                                <span class="h-2.5 w-2.5 rounded-full border border-slate-200" @style(['background-color: '.$vColor])></span>
                            @else
                                <span class="h-2.5 w-2.5 rounded-full border border-slate-200 bg-slate-100"></span>
                            @endif
                            <span>{{ is_array($vCat) ? ($vCat['name'] ?? '-') : '-' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Meta</div>
                        @php
                            $vGoal = $viewing['goal'] ?? null;
                        @endphp
                        <div class="mt-1 text-sm text-slate-900">
                            @if (is_array($vGoal))
                                <a href="{{ url('/app/goals/'.$vGoal['id']) }}" class="font-semibold text-brand-700 hover:text-brand-600">
                                    {{ $vGoal['name'] ?? '-' }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Origem</div>
                        @php
                            $vAcc = $viewing['bank_account'] ?? null;
                            $vCard = $viewing['credit_card'] ?? null;
                        @endphp
                        <div class="mt-1 text-sm text-slate-900">
                            @if (is_array($vCard))
                                {{ ($vCard['type'] ?? '') === 'debito' ? 'Cartão/Benefício' : 'Cartão' }}: {{ $vCard['name'] ?? '-' }}{{ ! empty($vCard['last4']) ? ' (**** '.$vCard['last4'].')' : '' }}{{ ! empty($vCard['type']) ? ' - '.$vCard['type'] : '' }}
                            @elseif (is_array($vAcc))
                                Conta: {{ $vAcc['name'] ?? '-' }}
                            @else
                                Dinheiro
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Data</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $viewing['date'] ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Tipo</div>
                        <div class="mt-2">
                            @if (($viewing['type'] ?? '') === 'debito')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Despesa</span>
                            @else
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Receita</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Valor</div>
                        <div class="mt-1 text-lg font-semibold text-slate-900">
                            R$ {{ number_format((float) ($viewing['amount'] ?? 0), 2, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold text-slate-600">Status</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $viewing['status'] ?? '-' }}</div>
                    </div>

                    @if (! empty($viewing['observations']))
                        <div class="sm:col-span-2">
                            <div class="text-xs font-semibold text-slate-600">Observações</div>
                            <div class="mt-1 whitespace-pre-wrap text-sm text-slate-700">{{ $viewing['observations'] }}</div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="$set('viewModalOpen', false)"
                        class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmDeleteOpen)
        <div class="fixed inset-0 z-[70] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('confirmDeleteOpen', false)"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Excluir lançamento?</div>
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
