<?php

use App\Livewire\App\Dashboard\Index;
use App\Livewire\App\Goals\Show;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('app')->group(function () {
    Route::get('/', Index::class);
    Route::get('/transactions', App\Livewire\App\Transactions\Index::class);
    Route::view('/transactions/new', 'app.transactions.new');
    Route::get('/goals', App\Livewire\App\Goals\Index::class);
    Route::get('/goals/{goal}', Show::class);
    Route::get('/categories', App\Livewire\App\Categories\Index::class);
    Route::get('/accounts', App\Livewire\App\Accounts\Index::class);
    Route::get('/credit-cards', App\Livewire\App\CreditCards\Index::class);
    Route::get('/admin/users', App\Livewire\App\Admin\Users\Index::class)->middleware('admin');
});
