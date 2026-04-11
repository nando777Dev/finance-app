<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm text-slate-500">Visão geral</div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
        </div>
        <div class="flex gap-2">
            <a
                href="{{ url('/app/transactions/new') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
            >
                Novo lançamento
            </a>
            <a
                href="{{ url('/app/transactions') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-900 hover:bg-slate-50"
            >
                Ver lançamentos
            </a>
        </div>
    </div>

    <livewire:dashboard-stats />

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Atividade</div>
                    <div class="text-xs text-slate-500">Resumo de lançamentos</div>
                </div>
                <a href="{{ url('/app/transactions') }}" class="text-sm font-medium text-brand-600 hover:text-brand-500">
                    Abrir
                </a>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <div class="text-xs text-slate-500">Hoje</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">0 lançamentos</div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-3">
                    <div class="text-xs text-slate-500">Este mês</div>
                    <div class="mt-1 text-lg font-semibold text-slate-900">0 lançamentos</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Ações rápidas</div>
            <div class="mt-4 flex flex-col gap-2">
                <a
                    href="{{ url('/app/categories') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-50"
                >
                    Gerenciar categorias
                </a>
                <a
                    href="{{ url('/app/accounts') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-50"
                >
                    Gerenciar contas bancárias
                </a>
                <a
                    href="{{ url('/app/credit-cards') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 hover:bg-slate-50"
                >
                    Gerenciar cartões
                </a>
            </div>
        </div>
    </div>
</div>
