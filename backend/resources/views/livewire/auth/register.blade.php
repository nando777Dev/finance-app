<div>
    <div class="text-xl font-semibold text-slate-900">Criar conta</div>
    <div class="mt-1 text-sm text-slate-500">Seu cadastro ficará pendente até aprovação do administrador.</div>

    @if ($success)
        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            Cadastro enviado. Aguarde a aprovação do administrador para acessar.
        </div>
    @endif

    <form wire:submit="submit" class="mt-6 space-y-4">
        <div>
            <label class="text-xs font-semibold text-slate-600">Nome</label>
            <input
                type="text"
                wire:model.defer="name"
                autocomplete="name"
                class="mt-1 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
            />
            @error('name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-600">E-mail</label>
            <input
                type="email"
                wire:model.defer="email"
                autocomplete="email"
                class="mt-1 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
            />
            @error('email') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-600">Senha</label>
            <input
                type="password"
                wire:model.defer="password"
                autocomplete="new-password"
                class="mt-1 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
            />
            @error('password') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-600">Confirmar senha</label>
            <input
                type="password"
                wire:model.defer="password_confirmation"
                autocomplete="new-password"
                class="mt-1 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
            />
            @error('password_confirmation') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
        </div>

        <button
            type="submit"
            class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
        >
            Enviar cadastro
        </button>

        <a href="{{ url('/login') }}" class="block text-center text-sm font-semibold text-brand-700 hover:text-brand-600">
            Já tenho conta
        </a>
    </form>
</div>

