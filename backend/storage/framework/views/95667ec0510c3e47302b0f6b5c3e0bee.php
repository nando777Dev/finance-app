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
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm focus:border-brand-600 focus:outline-none <?php echo e($preset !== 'custom' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-900'); ?>"
                        <?php if($preset !== 'custom'): echo 'disabled'; endif; ?>
                    />
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Fim</label>
                    <input
                        type="date"
                        wire:model.live="endDate"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm focus:border-brand-600 focus:outline-none <?php echo e($preset !== 'custom' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-slate-900'); ?>"
                        <?php if($preset !== 'custom'): echo 'disabled'; endif; ?>
                    />
                </div>

                <div class="flex items-end">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($preset === 'custom'): ?>
                        <button
                            type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                        >
                            Aplicar
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-slate-500">Saldo do mês</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_null($balanceDeltaPercent)): ?>
                    <div class="rounded-full bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                        <?php echo e($balanceDeltaPercent >= 0 ? '+' : ''); ?><?php echo e(number_format($balanceDeltaPercent, 1, ',', '.')); ?>%
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ <?php echo e(number_format($balanceCurrent, 2, ',', '.')); ?>

            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/2 rounded-full bg-brand-600"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-slate-500">Receitas (mês)</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_null($incomeDeltaPercent)): ?>
                    <div class="rounded-full bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                        <?php echo e($incomeDeltaPercent >= 0 ? '+' : ''); ?><?php echo e(number_format($incomeDeltaPercent, 1, ',', '.')); ?>%
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ <?php echo e(number_format($incomeCurrent, 2, ',', '.')); ?>

            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-2/3 rounded-full bg-brand-500"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div class="text-xs font-semibold text-red-600">Despesas (mês)</div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_null($expenseDeltaPercent)): ?>
                    <div class="rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">
                        <?php echo e($expenseDeltaPercent >= 0 ? '+' : ''); ?><?php echo e(number_format($expenseDeltaPercent, 1, ',', '.')); ?>%
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">
                R$ <?php echo e(number_format($expenseCurrent, 2, ',', '.')); ?>

            </div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/3 rounded-full bg-red-600"></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">Parcelas pendentes</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e($pendingInstallments); ?></div>
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

            <?php
                $max = 0;
                foreach ($monthlyFlowTrend as $p) {
                    $max = max($max, $p['income'] ?? 0, $p['expense'] ?? 0);
                }
            ?>

            <div class="mt-5 grid grid-cols-6 items-end gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthlyFlowTrend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $incomePct = $max > 0 ? (int) round((($point['income'] ?? 0) / $max) * 100) : 0;
                        $expensePct = $max > 0 ? (int) round((($point['expense'] ?? 0) / $max) * 100) : 0;
                        $incomePct = max(0, min(100, $incomePct));
                        $expensePct = max(0, min(100, $expensePct));
                    ?>
                    <div class="flex flex-col items-center gap-2">
                        <div class="h-28 w-full rounded-xl bg-slate-50 p-2">
                            <div class="flex h-full items-end gap-2">
                                <div class="w-1/2 rounded-lg bg-gradient-to-t from-emerald-500 to-emerald-700" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['height: '.$incomePct.'%']) ?>"></div>
                                <div class="w-1/2 rounded-lg bg-gradient-to-t from-red-500 to-red-700" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['height: '.$expensePct.'%']) ?>"></div>
                            </div>
                        </div>
                        <div class="text-[11px] font-semibold text-slate-500"><?php echo e($point['label']); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topExpenseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium text-slate-900"><?php echo e($row['name']); ?></div>
                            <div class="text-xs text-slate-500">R$ <?php echo e(number_format($row['total'], 2, ',', '.')); ?></div>
                        </div>
                        <div class="h-9 w-9 shrink-0 rounded-xl bg-slate-50"></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-sm text-slate-500">Sem dados neste mês.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
            <div class="text-sm font-semibold text-slate-900">Gastos por origem (mês)</div>
            <div class="mt-1 text-xs text-slate-500">Dinheiro x Contas x Cartões</div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($expenseByOrigin)): ?>
                <?php
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
                ?>
                <div class="mt-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center justify-center">
                        <div class="relative h-40 w-40 rounded-full" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background-image: '.$gradient]) ?>">
                            <div class="absolute inset-6 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <div class="flex-1 space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseByOrigin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 text-sm font-medium text-slate-900">
                                    <span class="h-2.5 w-2.5 rounded-full" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['background-color: '.($colors[$row['key']] ?? '#94a3b8')]) ?>"></span>
                                    <span><?php echo e($row['label']); ?></span>
                                </div>
                                <div class="text-sm font-semibold text-slate-700"><?php echo e(number_format((float) ($row['percent'] ?? 0), 1, ',', '.')); ?>%</div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-5 space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($expenseByOrigin)): ?>
                    <div class="text-sm text-slate-500">Sem dados suficientes para exibir.</div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $expenseByOrigin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $pct = (float) ($row['percent'] ?? 0);
                            $pctClamped = max(0, min(100, $pct));
                        ?>
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm font-semibold text-slate-900"><?php echo e($row['label']); ?></div>
                                <div class="rounded-full px-2 py-1 text-xs font-semibold <?php echo e($row['badge']); ?>">
                                    R$ <?php echo e(number_format((float) $row['total'], 2, ',', '.')); ?>

                                </div>
                            </div>
                            <div class="mt-3 h-2 w-full rounded-full bg-slate-100">
                                <div class="h-2 rounded-full <?php echo e($row['bar']); ?>" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['width: '.$pctClamped.'%']) ?>"></div>
                            </div>
                            <div class="mt-2 text-xs text-slate-500"><?php echo e(number_format($pct, 1, ',', '.')); ?>% do total</div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="text-sm font-semibold text-slate-900">Benefícios (limite do mês)</div>
                <div class="mt-1 text-xs text-slate-500">Ex.: Vale Alimentação</div>

                <div class="mt-5 space-y-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $benefitUsage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pct = (float) ($b['percent'] ?? 0);
                            $pctClamped = max(0, min(100, $pct));
                        ?>
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">
                                        <?php echo e($b['name']); ?>

                                        <span class="ml-2 rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                            <?php echo e($b['limit_type']); ?>

                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($b['brand'])): ?>
                                            <?php echo e($b['brand']); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($b['last4'])): ?>
                                            <?php echo e(! empty($b['brand']) ? '•' : ''); ?> **** <?php echo e($b['last4']); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-semibold text-slate-600">Gasto</div>
                                    <div class="text-sm font-semibold text-slate-900">
                                        R$ <?php echo e(number_format((float) $b['spent'], 2, ',', '.')); ?>

                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 h-2 w-full rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-red-600" style="<?php echo \Illuminate\Support\Arr::toCssStyles(['width: '.$pctClamped.'%']) ?>"></div>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                <span><?php echo e(number_format($pct, 1, ',', '.')); ?>% do limite</span>
                                <span>Saldo: R$ <?php echo e(number_format((float) $b['remaining'], 2, ',', '.')); ?></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-slate-500">Nenhum benefício com limite cadastrado.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">Saldos das contas</div>
                        <div class="mt-1 text-xs text-slate-500">Saldo atual por conta</div>
                    </div>
                    <a href="<?php echo e(url('/app/accounts')); ?>" class="text-sm font-medium text-brand-600 hover:text-brand-500">Abrir</a>
                </div>

                <div class="mt-4 space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bankAccountBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium text-slate-900"><?php echo e($acc['name']); ?></div>
                            </div>
                            <div class="text-right text-sm font-semibold text-slate-900">
                                R$ <?php echo e(number_format((float) $acc['balance'], 2, ',', '.')); ?>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-slate-500">Nenhuma conta cadastrada.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($bankAccountBalances)): ?>
                    <div class="mt-4 rounded-xl bg-slate-50 p-3">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold text-slate-600">Total</div>
                            <div class="text-sm font-semibold text-slate-900">
                                R$ <?php echo e(number_format((float) $bankAccountTotal, 2, ',', '.')); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-6 min-w-0">
                            <div class="truncate font-medium text-slate-900">
                                <?php echo e($t['description']); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t['installment_label']): ?>
                                    <span class="ml-2 rounded-full bg-slate-50 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                        <?php echo e($t['installment_label']); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="text-xs text-slate-500"><?php echo e($t['status']); ?></div>
                        </div>
                        <div class="col-span-2 text-slate-700"><?php echo e($t['type']); ?></div>
                        <div class="col-span-2 text-right font-semibold text-slate-900">
                            R$ <?php echo e(number_format($t['amount'], 2, ',', '.')); ?>

                        </div>
                        <div class="col-span-2 text-right text-slate-600"><?php echo e($t['date']); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhum lançamento ainda.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/dashboard-stats.blade.php ENDPATH**/ ?>