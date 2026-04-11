<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-slate-500">Administração</div>
            <h1 class="text-2xl font-semibold text-slate-900">Usuários</h1>
        </div>
        <a
            href="<?php echo e(url('/api/admin/users')); ?>"
            class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
        >
            Ver API
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">Pendentes</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e($pendingCount); ?></div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/3 rounded-full bg-amber-500"></div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">Ativos</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e($activeCount); ?></div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-2/3 rounded-full bg-emerald-600"></div>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-xs font-semibold text-slate-500">Inativos</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900"><?php echo e($inactiveCount); ?></div>
            <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                <div class="h-1.5 w-1/2 rounded-full bg-red-600"></div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="$set('tab', 'pending')"
                    class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-sm font-semibold transition <?php echo e($tab === 'pending' ? 'bg-amber-600 text-white' : 'border border-slate-200 bg-white text-slate-900 hover:bg-slate-50'); ?>"
                    <?php if(! $hasApproval): echo 'disabled'; endif; ?>
                >
                    Pendentes
                </button>
                <button
                    type="button"
                    wire:click="$set('tab', 'all')"
                    class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-sm font-semibold transition <?php echo e($tab === 'all' ? 'bg-brand-600 text-white' : 'border border-slate-200 bg-white text-slate-900 hover:bg-slate-50'); ?>"
                >
                    Todos
                </button>
                <button
                    type="button"
                    wire:click="$set('tab', 'inactive')"
                    class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-sm font-semibold transition <?php echo e($tab === 'inactive' ? 'bg-red-600 text-white' : 'border border-slate-200 bg-white text-slate-900 hover:bg-slate-50'); ?>"
                >
                    Inativos
                </button>
            </div>

            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por nome/e-mail..."
                class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none md:w-80"
            />
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $hasApproval): ?>
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Para usar aprovação, rode as migrations novas (approved_at).
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-4">Nome</div>
                <div class="col-span-4">E-mail</div>
                <div class="col-span-2">Status</div>
                <div class="col-span-2 text-right">Ações</div>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $pending = $hasApproval && ! $u->is_admin && $u->approved_at === null;
                    ?>
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-4 min-w-0">
                            <div class="truncate font-semibold text-slate-900"><?php echo e($u->name); ?></div>
                            <div class="mt-1 text-xs text-slate-500"><?php echo e(optional($u->created_at)->format('d/m/Y H:i')); ?></div>
                        </div>
                        <div class="col-span-4 min-w-0 truncate text-slate-700"><?php echo e($u->email); ?></div>
                        <div class="col-span-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->is_admin): ?>
                                <span class="rounded-full bg-brand-600/10 px-2 py-0.5 text-xs font-semibold text-brand-700">Admin</span>
                            <?php elseif($pending): ?>
                                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">Pendente</span>
                            <?php elseif(! $u->is_active): ?>
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Inativo</span>
                            <?php else: ?>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Ativo</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="col-span-2 flex justify-end gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pending): ?>
                                <button
                                    type="button"
                                    wire:click="approve(<?php echo e($u->id); ?>)"
                                    class="inline-flex h-9 items-center justify-center rounded-xl bg-emerald-600 px-3 text-xs font-semibold text-white hover:bg-emerald-500"
                                    title="Aprovar"
                                >
                                    Aprovar
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $u->is_admin): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($u->is_active): ?>
                                    <button
                                        type="button"
                                        wire:click="deactivate(<?php echo e($u->id); ?>)"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-700 hover:bg-red-100"
                                        title="Desativar"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                            <path fill-rule="evenodd" d="M4.5 11.25a.75.75 0 0 0 0 1.5h15a.75.75 0 0 0 0-1.5h-15Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                <?php else: ?>
                                    <button
                                        type="button"
                                        wire:click="activate(<?php echo e($u->id); ?>)"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100"
                                        title="Ativar"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                            <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <button
                                type="button"
                                wire:click="openEdit(<?php echo e($u->id); ?>)"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100"
                                title="Editar"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="h-5 w-5 fill-current">
                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.5 1.5 3.712 3.712 1.5-1.5a2.625 2.625 0 0 0 0-3.712Z" />
                                    <path d="m18.75 8.25-3.712-3.712L3.75 15.826V19.5h3.674L18.75 8.25Z" />
                                </svg>
                            </button>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $u->is_admin): ?>
                                <button
                                    type="button"
                                    wire:click="askDelete(<?php echo e($u->id); ?>)"
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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-10 text-center text-sm text-slate-500">Nenhum usuário encontrado.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">
                <?php echo e($users->links()); ?>

            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <div class="text-sm font-semibold text-slate-900">Últimos cadastrados</div>
        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
            <div class="grid grid-cols-12 gap-2 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                <div class="col-span-5">Nome</div>
                <div class="col-span-5">E-mail</div>
                <div class="col-span-2 text-right">Data</div>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="grid grid-cols-12 items-center gap-2 px-4 py-3 text-sm">
                        <div class="col-span-5 truncate font-semibold text-slate-900"><?php echo e($r->name); ?></div>
                        <div class="col-span-5 truncate text-slate-700"><?php echo e($r->email); ?></div>
                        <div class="col-span-2 text-right text-slate-600"><?php echo e(optional($r->created_at)->format('d/m/Y')); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editModalOpen): ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="$set('editModalOpen', false)"></div>
            <div class="relative w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-slate-900">Editar usuário</div>
                        <div class="text-sm text-slate-500">Apenas nome e e-mail.</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-900 hover:bg-slate-50"
                        wire:click="$set('editModalOpen', false)"
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
                    <div>
                        <label class="text-xs font-semibold text-slate-600">E-mail</label>
                        <input
                            type="email"
                            wire:model.defer="email"
                            class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-600 focus:outline-none"
                        />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
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
                        wire:click="$set('editModalOpen', false)"
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
                <div class="text-lg font-semibold text-slate-900">Excluir usuário?</div>
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

<?php /**PATH /var/www/resources/views/livewire/app/admin/users/index.blade.php ENDPATH**/ ?>