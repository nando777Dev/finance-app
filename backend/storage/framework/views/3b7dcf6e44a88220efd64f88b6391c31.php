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
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-400">Lançamentos</div>
                <h1 class="text-2xl font-semibold text-slate-100">Transações</h1>
            </div>
            <a
                href="<?php echo e(url('/app/transactions/new')); ?>"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
            >
                Novo
            </a>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm font-semibold text-slate-100">Lista</div>
                <div class="flex gap-2">
                    <a
                        href="<?php echo e(url('/api/transactions?grouped=true')); ?>"
                        class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-800 bg-slate-950 px-3 text-sm font-medium text-slate-200 hover:bg-slate-900"
                    >
                        API agrupada
                    </a>
                    <a
                        href="<?php echo e(url('/api/transactions')); ?>"
                        class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-800 bg-slate-950 px-3 text-sm font-medium text-slate-200 hover:bg-slate-900"
                    >
                        API normal
                    </a>
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-800">
                <div class="grid grid-cols-12 gap-2 bg-slate-900/40 px-4 py-3 text-xs font-semibold text-slate-300">
                    <div class="col-span-5">Descrição</div>
                    <div class="col-span-3">Categoria</div>
                    <div class="col-span-2 text-right">Valor</div>
                    <div class="col-span-2 text-right">Data</div>
                </div>
                <div class="px-4 py-8 text-center text-sm text-slate-400">
                    Em seguida, podemos transformar essa tela em um componente Livewire com filtros, paginação e agrupamento por pai.
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

<?php /**PATH /var/www/resources/views/app/transactions/index.blade.php ENDPATH**/ ?>