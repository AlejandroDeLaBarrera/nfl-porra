<?php

use Livewire\Component;
use App\Models\Round;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public Round $round;
    public $games;
    public $scores = [];
    public $correctPredictions = 0;
    public $errorMessage = '';
    public $successMessage = '';

    public function mount()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->games = $this->round->games;

        foreach ($this->games as $game) {
            $this->scores[$game->id] = [
                'home' => $game->home_score,
                'away' => $game->away_score,
            ];
        }
        $this->correctPredictions = $this->round->correctPredictionsForUser(auth()->user());
    }

    public function saveResults()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        if (!$this->round->isClosed()) {
            return;
        }

        $this->validate([
            'scores.*.home' => 'required|integer|min:0',
            'scores.*.away' => 'required|integer|min:0',
        ]);

        foreach ($this->scores as $gameId => $score) {
            $game = $this->round->games()->find($gameId);

            if ($game) {
                $game->update([
                    'home_score' => $score['home'],
                    'away_score' => $score['away'],
                ]);
            }
        }
        $this->successMessage = 'Resultados guardados correctamente.';
    }

    public function finishRound()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        if (!$this->round->isClosed()) {
            return;
        }

        $this->validate([
            'scores.*.home' => 'required|integer|min:0',
            'scores.*.away' => 'required|integer|min:0',
        ]);

        foreach ($this->scores as $score) {
            if ($score['home'] === null || $score['away'] === null) {
                $this->errorMessage = 'Debes introducir el resultado de todos los partidos.';
                return;
            }
        }

        DB::transaction(function () {
            foreach ($this->scores as $gameId => $score) {
                $game = $this->round->games()->find($gameId);

                if ($game) {
                    $game->update([
                        'home_score' => $score['home'],
                        'away_score' => $score['away'],
                    ]);
                }
            }

            $this->round->refresh();

            $this->round->saveResults();

            $this->round->update([
                'status' => 'finished',
            ]);
        });

        $this->errorMessage = '';
        $this->successMessage = 'Jornada finalizada correctamente.';
    }
};
?>

<div>
    <h1>Administración de resultados</h1>

    @if ($successMessage)
        <p class="text-green-600">
            {{ $successMessage }}
        </p>
    @endif

    @if ($errorMessage)
        <p class="text-red-600">
            {{ $errorMessage }}
        </p>
    @endif

    <p>
        Temporada: {{ $round->season }}
    </p>

    <p>
        Week {{ $round->week }}
    </p>

    @foreach ($games as $game)
        <div wire:key="game-{{ $game->id }}">
            <h2>
                {{ $game->home_team }}
                vs
                {{ $game->away_team }}
            </h2>

            <input type="number" min="0" wire:model="scores.{{ $game->id }}.home">

            -

            <input type="number" min="0" wire:model="scores.{{ $game->id }}.away">
        </div>
    @endforeach

    @if ($round->isClosed())
        <button type="button" wire:click="saveResults">
            Guardar resultados
        </button>

        <button type="button" wire:click="finishRound">
            Finalizar jornada
        </button>
    @else
        <p>
            Jornada finalizada. Los resultados ya no pueden modificarse.
        </p>
    @endif

    <p>
        {{ auth()->user()->name }}: {{ $correctPredictions }} aciertos
    </p>

</div>
