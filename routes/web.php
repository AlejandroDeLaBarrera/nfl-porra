<?php

use Illuminate\Support\Facades\Route;
use App\Models\Round;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('rounds/{round}', 'round-predictions')
        ->name('rounds.predictions');

    Route::livewire('rounds/{round}/results', 'round-results')
        ->name('rounds.results');

    Route::livewire('rounds/{round}/scoreboard', 'round-scoreboard')
    ->name('rounds.scoreboard');

    Route::livewire('rounds', 'rounds')
    ->name('rounds');

    Route::livewire('standings', 'standings')
    ->name('standings');

    Route::livewire('admin/rounds', 'admin-rounds')
    ->name('admin.rounds');

    Route::livewire('admin/rounds/create', 'admin-round-create')
    ->name('admin.rounds.create');

    Route::livewire('admin/rounds/{round}', 'admin-round')
    ->name('admin.round');
});

require __DIR__.'/settings.php';
