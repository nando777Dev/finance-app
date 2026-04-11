<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
        <title><?php echo e(config('app.name', 'Finance App')); ?></title>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_file(public_path('hot')) || is_file(public_path('build/manifest.json'))): ?>
            <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    </head>
    <body class="min-h-dvh bg-slate-100 text-slate-900 antialiased">
        <div class="pointer-events-none fixed inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-b from-white via-indigo-50 to-slate-200"></div>
            <div class="absolute -top-28 left-1/3 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-brand-500/25 blur-3xl"></div>
            <div class="absolute top-24 right-12 h-80 w-80 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-10 h-96 w-96 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
        </div>
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 py-3">
                <div class="flex items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <details class="relative md:hidden">
                            <summary
                                class="flex cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50"
                                aria-label="Menu"
                            >
                                ☰
                            </summary>
                            <div class="absolute left-0 mt-2 w-72 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                                <a href="<?php echo e(url('/app')); ?>" class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-slate-50">
                                    Dashboard
                                </a>
                                <div class="mt-1 rounded-xl bg-slate-50 p-2">
                                    <div class="px-2 pb-1 text-[11px] font-semibold text-slate-500">Lançamentos</div>
                                    <a href="<?php echo e(url('/app/transactions')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                        Listar
                                    </a>
                                    <a href="<?php echo e(url('/app/transactions/new')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                        Novo lançamento
                                    </a>
                                </div>
                                <a href="<?php echo e(url('/app/goals')); ?>" class="mt-1 block rounded-xl px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-50">
                                    Metas
                                </a>
                                <div class="mt-1 rounded-xl bg-slate-50 p-2">
                                    <div class="px-2 pb-1 text-[11px] font-semibold text-slate-500">Cadastros</div>
                                    <a href="<?php echo e(url('/app/categories')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                        Categorias
                                    </a>
                                    <a href="<?php echo e(url('/app/accounts')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                        Contas bancárias
                                    </a>
                                    <a href="<?php echo e(url('/app/credit-cards')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                        Cartões
                                    </a>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()?->is_admin): ?>
                                    <div class="mt-1 rounded-xl bg-slate-50 p-2">
                                        <div class="px-2 pb-1 text-[11px] font-semibold text-slate-500">Administração</div>
                                        <a href="<?php echo e(url('/app/admin/users')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                            Usuários
                                        </a>
                                        <a href="<?php echo e(url('/api/documentation')); ?>" class="block rounded-lg px-2 py-2 text-sm text-slate-900 hover:bg-white">
                                            Swagger
                                        </a>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </details>

                        <a href="<?php echo e(url('/app')); ?>" class="flex items-center gap-3">
                            <span class="h-9 w-9 rounded-xl bg-gradient-to-br from-brand-600 to-brand-900"></span>
                            <div class="leading-tight">
                                <div class="text-sm font-semibold text-slate-900"><?php echo e(config('app.name', 'Finance App')); ?></div>
                                <div class="text-xs text-slate-500">Gestão financeira</div>
                            </div>
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden lg:block">
                            <input
                                type="text"
                                placeholder="Buscar..."
                                class="h-10 w-72 rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
                            />
                        </div>

                        <details class="group relative">
                            <summary
                                class="flex cursor-pointer list-none items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 hover:bg-slate-50"
                            >
                                <span class="h-8 w-8 rounded-xl bg-gradient-to-br from-brand-600 to-brand-900"></span>
                                <div class="hidden sm:block">
                                    <div class="text-sm font-semibold leading-tight text-slate-900"><?php echo e(auth()->user()?->name ?? 'Conta'); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e(auth()->user()?->email); ?></div>
                                </div>
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700">
                                    <?php echo e(auth()->user()?->is_admin ? 'Admin' : 'Usuário'); ?>

                                </span>
                                <span class="text-slate-500 transition group-open:rotate-180">▾</span>
                            </summary>

                            <div class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                                <a href="<?php echo e(url('/api/documentation')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                                    Documentação API
                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()?->is_admin): ?>
                                    <a href="<?php echo e(url('/app/admin/users')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                                        Admin
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-3 hidden max-w-7xl items-center gap-2 px-4 pb-3 md:flex">
                <a
                    href="<?php echo e(url('/app')); ?>"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-500 hover:shadow-md"
                >
                    Dashboard
                </a>

                <details class="group relative">
                    <summary
                        class="flex cursor-pointer list-none items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 hover:shadow-sm"
                    >
                        Lançamentos
                        <span class="text-slate-400 transition group-open:rotate-180">▾</span>
                    </summary>
                    <div class="absolute left-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                        <a href="<?php echo e(url('/app/transactions')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                            Listar
                        </a>
                        <a href="<?php echo e(url('/app/transactions/new')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                            Novo lançamento
                        </a>
                    </div>
                </details>

                <a
                    href="<?php echo e(url('/app/goals')); ?>"
                    class="inline-flex h-10 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 hover:shadow-sm"
                >
                    Metas
                </a>

                <details class="group relative">
                    <summary
                        class="flex cursor-pointer list-none items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 hover:shadow-sm"
                    >
                        Cadastros
                        <span class="text-slate-400 transition group-open:rotate-180">▾</span>
                    </summary>
                    <div class="absolute left-0 mt-2 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                        <a href="<?php echo e(url('/app/categories')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                            Categorias
                        </a>
                        <a href="<?php echo e(url('/app/accounts')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                            Contas bancárias
                        </a>
                        <a href="<?php echo e(url('/app/credit-cards')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                            Cartões
                        </a>
                    </div>
                </details>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()?->is_admin): ?>
                    <details class="group relative">
                        <summary
                            class="flex cursor-pointer list-none items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 hover:shadow-sm"
                        >
                            Admin
                            <span class="text-slate-400 transition group-open:rotate-180">▾</span>
                        </summary>
                        <div class="absolute left-0 mt-2 w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                            <a href="<?php echo e(url('/app/admin/users')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                                Usuários
                            </a>
                            <a href="<?php echo e(url('/api/documentation')); ?>" class="block rounded-xl px-3 py-2 text-sm text-slate-900 hover:bg-slate-50">
                                Swagger
                            </a>
                        </div>
                    </details>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </header>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_file(public_path('hot')) && ! is_file(public_path('build/manifest.json'))): ?>
            <div class="border-b border-slate-800 bg-slate-950 px-4 py-3 text-sm text-slate-200">
                Assets do Vite não encontrados. Rode npm install e depois npm run dev (ou npm run build) dentro de backend/.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <main class="relative px-4 py-8">
            <div class="pointer-events-none absolute inset-x-0 -top-10 h-80 bg-gradient-to-r from-brand-500/25 via-indigo-500/15 to-fuchsia-500/25 blur-3xl"></div>
            <div class="relative mx-auto max-w-7xl">
                <div
                    class="w-full rounded-3xl bg-white/80 p-6 shadow-[0_30px_90px_-55px_rgba(79,70,229,0.55)] ring-1 ring-white/70 backdrop-blur"
                >
                    <?php echo e($slot); ?>

                </div>
            </div>
        </main>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH /var/www/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>