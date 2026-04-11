<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-400">Administração</div>
                <h1 class="text-2xl font-semibold text-slate-100">Usuários</h1>
            </div>
            <a
                href="{{ url('/api/admin/users') }}"
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
</x-layouts.app>

