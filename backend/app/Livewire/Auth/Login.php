<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect('/app', navigate: true);
        }
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $validated['remember'])) {
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        request()->session()->regenerate();

        $user = Auth::user();
        if ($user && ! $user->is_admin && $user->approved_at === null) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Aguarde enquanto um administrador faz a liberação do seu acesso.',
            ]);
        }

        if ($user && ! $user->is_active) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Conta inativa. Entre em contato com o administrador.',
            ]);
        }

        $this->redirect('/app', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
