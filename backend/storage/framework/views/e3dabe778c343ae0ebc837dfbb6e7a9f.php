<div class="space-y-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="text-sm font-semibold text-slate-900">Categorias</div>
            <div class="text-sm text-slate-500">Gerencie suas categorias de receitas e despesas.</div>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar..."
                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none sm:w-64"
            />

            <button
                type="button"
                wire:click="openCreate"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
            >
                Adicionar categoria
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
            <div class="col-span-6">Nome</div>
            <div class="col-span-4">Criada em</div>
            <div class="col-span-2 text-right">Ações</div>
        </div>

        <div class="divide-y divide-slate-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                    <div class="col-span-6">
                        <div class="font-medium text-slate-900"><?php echo e($category->name); ?></div>
                        <div class="text-xs text-slate-500"><?php echo e($category->type); ?></div>
                    </div>
                    <div class="col-span-4 text-slate-600">
                        <?php echo e(optional($category->created_at)->format('d/m/Y H:i')); ?>

                    </div>
                    <div class="col-span-2 flex justify-end">
                        <details class="relative">
                            <summary
                                class="flex cursor-pointer list-none items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-50"
                            >
                                Ações
                                <span class="text-slate-500">▾</span>
                            </summary>
                            <div class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                                <button
                                    type="button"
                                    wire:click="openEdit(<?php echo e($category->id); ?>)"
                                    class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-900 hover:bg-slate-50"
                                >
                                    Editar
                                </button>
                                <button
                                    type="button"
                                    wire:click="askDelete(<?php echo e($category->id); ?>)"
                                    class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                >
                                    Excluir
                                </button>
                            </div>
                        </details>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhuma categoria encontrada.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            <?php echo e($categories->links()); ?>

        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modalOpen): ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('modalOpen', false)"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">
                            <?php echo e($editingId ? 'Editar categoria' : 'Nova categoria'); ?>

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

                <div class="mt-6 grid gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Nome</label>
                        <input
                            type="text"
                            wire:model.defer="name"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-1 text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Tipo</label>
                            <select
                                wire:model.defer="type"
                                class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="despesa">despesa</option>
                                <option value="receita">receita</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-1 text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-600">Cor</label>
                            <input
                                type="text"
                                wire:model.defer="color"
                                placeholder="#FF5733"
                                class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                            />
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-1 text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Ícone</label>
                        <input
                            type="text"
                            wire:model.defer="icon"
                            placeholder="utensils"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-1 text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirmDeleteOpen): ?>
        <div class="fixed inset-0 z-[70] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('confirmDeleteOpen', false)"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Excluir categoria?</div>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php /**PATH /var/www/resources/views/livewire/app/categories/index.blade.php ENDPATH**/ ?>