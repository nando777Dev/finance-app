<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-sm text-slate-400">Visão geral</div>
                <h1 class="text-2xl font-semibold text-slate-100">Dashboard</h1>
            </div>
            <div class="flex gap-2">
                <a
                    href="<?php echo e(url('/app/transactions/new')); ?>"
                    class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
                >
                    Novo lançamento
                </a>
                <a
                    href="<?php echo e(url('/app/transactions')); ?>"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-950 px-4 text-sm font-semibold text-slate-200 hover:bg-slate-900"
                >
                    Ver lançamentos
                </a>
            </div>
        </div>

        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard-stats', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-759166622-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-slate-100">Atividade</div>
                        <div class="text-xs text-slate-400">Resumo de lançamentos</div>
                    </div>
                    <a href="<?php echo e(url('/app/transactions')); ?>" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                        Abrir
                    </a>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-3">
                        <div class="text-xs text-slate-400">Hoje</div>
                        <div class="mt-1 text-lg font-semibold text-slate-100">0 lançamentos</div>
                    </div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950 p-3">
                        <div class="text-xs text-slate-400">Este mês</div>
                        <div class="mt-1 text-lg font-semibold text-slate-100">0 lançamentos</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                <div class="text-sm font-semibold text-slate-100">Ações rápidas</div>
                <div class="mt-4 flex flex-col gap-2">
                    <a
                        href="<?php echo e(url('/app/categories')); ?>"
                        class="rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm font-medium text-slate-200 hover:bg-slate-900"
                    >
                        Gerenciar categorias
                    </a>
                    <a
                        href="<?php echo e(url('/api/documentation')); ?>"
                        class="rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm font-medium text-slate-200 hover:bg-slate-900"
                    >
                        Ver Swagger
                    </a>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>

<?php /**PATH /var/www/resources/views/app/dashboard.blade.php ENDPATH**/ ?>