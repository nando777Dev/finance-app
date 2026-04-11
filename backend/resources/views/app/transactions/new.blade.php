<x-layouts.app>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-400">Lançamentos</div>
                <h1 class="text-2xl font-semibold text-slate-100">Novo lançamento</h1>
            </div>
            <a
                href="{{ url('/app/transactions') }}"
                class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-950 px-4 text-sm font-semibold text-slate-200 hover:bg-slate-900"
            >
                Voltar
            </a>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                <div class="text-sm font-semibold text-slate-100">Lançamento simples</div>
                <div class="mt-3 text-sm text-slate-400">
                    Podemos implementar esse formulário via Livewire e enviar para a API /api/transactions.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-950 p-4">
                <div class="text-sm font-semibold text-slate-100">Compra parcelada</div>
                <div class="mt-3 text-sm text-slate-400">
                    Endpoint pronto: POST /api/transactions/installments (cria 1 pai + N filhas).
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

