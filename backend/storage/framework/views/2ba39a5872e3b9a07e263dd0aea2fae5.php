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
                <div class="text-sm text-slate-400">Administração</div>
                <h1 class="text-2xl font-semibold text-slate-100">Usuários</h1>
            </div>
            <a
                href="<?php echo e(url('/api/admin/users')); ?>"
                class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-950 px-4 text-sm font-semibold text-slate-200 hover:bg-slate-900"
            >
                Ver API
            </a>
        </div>

        <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
            <div class="text-sm font-semibold text-slate-100">Em breve</div>
            <div class="mt-2 text-sm text-slate-400">
                Vamos transformar essa tela em um Livewire com paginação, ativar/desativar e edição.
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

<?php /**PATH /var/www/resources/views/app/admin/users.blade.php ENDPATH**/ ?>