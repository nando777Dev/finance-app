<div class="space-y-6">
    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">Filtros</div>
                <div class="text-xs text-slate-500">Selecione um período para atualizar os indicadores.</div>
            </div>

            <form wire:submit.prevent="applyFilter" class="grid gap-3 sm:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600">Período</label>
                    <select
                        wire:model.live="preset"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                    >
                        <option value="month">Este mês</option>
                        <option value="last_30">Últimos 30 dias</option>
                        <option value="last_90">Últimos 90 dias</option>
                        <option value="year">Este ano</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Início</label>
                    <input
                        type="date"
                        wire:model.live="startDate"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm focus:border-brand-600 focus:outline-none {{ $preset !== 'custom' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-900' }}"
                        @disabled($preset !== 'custom')
                    />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Fim</label>
                    <input
                        type="date"
                        wire:model.live="endDate"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm focus:border-brand-600 focus:outline-none {{ $preset !== 'custom' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-900' }}"
                        @disabled($preset !== 'custom')
                    />
                </div>

                <div class="flex items-end">
                    @if ($preset === 'custom')
                        <button
                            type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                        >
                            Aplicar
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-slate-500">Saldo do mês</div>
                @if (! is_null($balanceDeltaPercent))
                    <div class="rounded-full bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                        {{ $balanceDeltaPercent >= 0 ? '+' : '' }}{{ number_format($balanceDeltaPercent, 1, ',', '.') }}%
                    </div>
                @endif
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ {{ number_format($balanceCurrent, 2, ',', '.') }}
            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/2 rounded-full bg-brand-600"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-slate-500">Receitas (mês)</div>
                @if (! is_null($incomeDeltaPercent))
                    <div class="rounded-full bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                        {{ $incomeDeltaPercent >= 0 ? '+' : '' }}{{ number_format($incomeDeltaPercent, 1, ',', '.') }}%
                    </div>
                @endif
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ {{ number_format($incomeCurrent, 2, ',', '.') }}
            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-2/3 rounded-full bg-brand-500"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-red-600">Despesas (mês)</div>
                @if (! is_null($expenseDeltaPercent))
                    <div class="rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">
                        {{ $expenseDeltaPercent >= 0 ? '+' : '' }}{{ number_format($expenseDeltaPercent, 1, ',', '.') }}%
                    </div>
                @endif
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ {{ number_format($expenseCurrent, 2, ',', '.') }}
            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/3 rounded-full bg-red-600"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">Parcelas pendentes</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $pendingInstallments }}</div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/4 rounded-full bg-brand-800"></div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Evolução (últimos 6 meses)</div>
                    <div class="text-xs text-slate-500">Receitas x Despesas</div>
                </div>
            </div>

            @php
                $max = 0;
                foreach ($monthlyFlowTrend as $p) {
                    $max = max($max, $p['income'] ?? 0, $p['expense'] ?? 0);
                }
            @endphp

            <div class="mt-5 grid grid-cols-6 items-end gap-3">
                @foreach ($monthlyFlowTrend as $point)
                    @php
                        $incomePct = $max > 0 ? (int) round((($point['income'] ?? 0) / $max) * 100) : 0;
                        $expensePct = $max > 0 ? (int) round((($point['expense'] ?? 0) / $max) * 100) : 0;
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
                        <div class="text-[11px] font-semibold text-slate-500">{{ $point['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center gap-4 text-xs font-semibold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    <span>Receitas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-red-600"></span>
                    <span>Despesas</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Maiores gastos (mês)</div>
            <div class="mt-1 text-xs text-slate-500">Top categorias por valor</div>

            <div class="mt-4 space-y-3">
                @forelse ($topExpenseCategories as $row)
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-slate-900">{{ $row['name'] }}</div>
                            <div class="text-xs text-slate-500">R$ {{ number_format($row['total'], 2, ',', '.') }}</div>
                        </div>
                        <div class="h-9 w-9 shrink-0 rounded-xl bg-slate-50"></div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Sem dados neste mês.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
            <div class="text-sm font-semibold text-slate-900">Gastos por origem (mês)</div>
            <div class="mt-1 text-xs text-slate-500">Dinheiro x Contas x Cartões</div>

            @if (! empty($expenseByOrigin))
                @php
                    $colors = [
                        'cash' => '#334155',
                        'bank' => '#4f46e5',
                        'card' => '#dc2626',
                    ];
                    $acc = 0;
                    $stops = [];
                    foreach ($expenseByOrigin as $row) {
                        $key = $row['key'] ?? '';
                        $pct = max(0, (float) ($row['percent'] ?? 0));
                        $start = $acc;
                        $acc += $pct;
                        $color = $colors[$key] ?? '#94a3b8';
                        $stops[] = $color.' '.$start.'% '.$acc.'%';
                    }
                    $gradient = 'conic-gradient('.implode(', ', $stops).')';
                @endphp
                <div class="mt-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center justify-center">
                        <div class="relative h-40 w-40 rounded-full" @style(['background-image: '.$gradient])>
                            <div class="absolute inset-6 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2">
                        @foreach ($expenseByOrigin as $row)
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                    <span class="h-2.5 w-2.5 rounded-full" @style(['background-color: '.($colors[$row['key']] ?? '#94a3b8')])></span>
                                    <span>{{ $row['label'] }}</span>
                                </div>
                                <div class="text-sm font-semibold text-slate-700">{{ number_format((float) ($row['percent'] ?? 0), 1, ',', '.') }}%</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-5 space-y-4">
                @if (empty($expenseByOrigin))
                    <div class="text-sm text-slate-500">Sem dados suficientes para exibir.</div>
                @else
                    @foreach ($expenseByOrigin as $row)
                        @php
                            $pct = (float) ($row['percent'] ?? 0);
                            $pctClamped = max(0, min(100, $pct));
                        @endphp
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-900">{{ $row['label'] }}</div>
                                <div class="rounded-full px-2 py-1 text-xs font-semibold {{ $row['badge'] }}">
                                    R$ {{ number_format((float) $row['total'], 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="mt-3 h-2 w-full rounded-full bg-slate-100">
                                <div class="h-2 rounded-full {{ $row['bar'] }}" @style(['width: '.$pctClamped.'%'])></div>
                            </div>
                            <div class="mt-2 text-xs text-slate-500">{{ number_format($pct, 1, ',', '.') }}% do total</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-sm font-semibold text-slate-900">Benefícios (limite do mês)</div>
                <div class="mt-1 text-xs text-slate-500">Ex.: Vale Alimentação</div>

                <div class="mt-5 space-y-4">
                    @forelse ($benefitUsage as $b)
                        @php
                            $pct = (float) ($b['percent'] ?? 0);
                            $pctClamped = max(0, min(100, $pct));
                        @endphp
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">
                                        {{ $b['name'] }}
                                        <span class="ml-2 rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                            {{ $b['limit_type'] }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        @if (! empty($b['brand']))
                                            {{ $b['brand'] }}
                                        @endif
                                        @if (! empty($b['last4']))
                                            {{ ! empty($b['brand']) ? '•' : '' }} **** {{ $b['last4'] }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-semibold text-slate-600">Gasto</div>
                                    <div class="text-sm font-semibold text-slate-900">
                                        R$ {{ number_format((float) $b['spent'], 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 h-2 w-full rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-red-600" @style(['width: '.$pctClamped.'%'])></div>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <span>{{ number_format($pct, 1, ',', '.') }}% do limite</span>
                                <span>Saldo: R$ {{ number_format((float) $b['remaining'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Nenhum benefício com limite cadastrado.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Saldos das contas</div>
                        <div class="mt-1 text-xs text-slate-500">Saldo atual por conta</div>
                    </div>
                    <a href="{{ url('/app/accounts') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">Abrir</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($bankAccountBalances as $acc)
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium text-slate-900">{{ $acc['name'] }}</div>
                            </div>
                            <div class="text-right text-sm font-semibold text-slate-900">
                                R$ {{ number_format((float) $acc['balance'], 2, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Nenhuma conta cadastrada.</div>
                    @endforelse
                </div>

                @if (! empty($bankAccountBalances))
                    <div class="mt-4 rounded-xl bg-slate-50 p-3">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold text-slate-600">Total</div>
                            <div class="text-sm font-semibold text-slate-900">
                                R$ {{ number_format((float) $bankAccountTotal, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">Últimos lançamentos</div>
                <div class="text-xs text-slate-500">Movimentações recentes</div>
            </div>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-6">Descrição</div>
                <div class="col-span-2">Tipo</div>
                <div class="col-span-2 text-right">Valor</div>
                <div class="col-span-2 text-right">Data</div>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentTransactions as $t)
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-6 min-w-0">
                            <div class="truncate font-medium text-slate-900">
                                {{ $t['description'] }}
                                @if ($t['installment_label'])
                                    <span class="ml-2 rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        {{ $t['installment_label'] }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">{{ $t['status'] }}</div>
                        </div>
                        <div class="col-span-2 text-slate-700">{{ $t['type'] }}</div>
                        <div class="col-span-2 text-right font-semibold text-slate-900">
                            R$ {{ number_format($t['amount'], 2, ',', '.') }}
                        </div>
                        <div class="col-span-2 text-right text-slate-600">{{ $t['date'] }}</div>
                    </div>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhum lançamento ainda.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
