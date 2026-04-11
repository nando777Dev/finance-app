<div>
    <div class="text-xl font-semibold text-slate-900">Login</div>
    <div class="mt-1 text-sm text-slate-500">Entre com seu e-mail e senha.</div>

    <form wire:submit="submit" class="mt-6 space-y-4">
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
                autocomplete="current-password"
                class="mt-1 h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-600 focus:outline-none"
            />
            @error('password') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" wire:model="remember" class="h-4 w-4 rounded border-slate-300" />
            Lembrar de mim
        </label>

        <button
            type="submit"
            class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-500"
        >
            Entrar
        </button>
    </form>

    <a href="{{ url('/register') }}" class="mt-4 block text-center text-sm font-semibold text-brand-700 hover:text-brand-600">
        Criar conta
    </a>
</div>
