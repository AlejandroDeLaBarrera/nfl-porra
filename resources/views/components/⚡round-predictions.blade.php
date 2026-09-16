<?php

use Livewire\Component;
use App\Models\Round;
use App\Models\Prediction;

new class extends Component {
    public Round $round;
    public $games;
    public $predictions = [];
    public $allPredictions = [];
    public $errorMessage = '';
    public $successMessage = '';
    public $status;

    public function mount()
    {
        $this->games = $this->round->games;

        $this->predictions = auth()
            ->user()
            ->predictions()
            ->whereIn('game_id', $this->games->pluck('id'))
            ->pluck('prediction', 'game_id')
            ->toArray();

        $this->status = $this->round->status;

        if ($this->round->isClosed() || $this->round->isFinished()) {
            $this->allPredictions = \App\Models\Prediction::query()
                ->with('user')
                ->whereIn('game_id', $this->games->pluck('id'))
                ->get();
        }
    }

    public function savePredictions()
    {
        $this->errorMessage = '';
        $this->successMessage = '';

        if (!$this->round->predictionsAreOpen()) {
            $this->errorMessage = 'La jornada ya no está disponible para realizar predicciones.';
            return;
        }

        if (count($this->predictions) !== $this->games->count()) {
            $this->errorMessage = 'Debes seleccionar un resultado para todos los partidos.';
            return;
        }

        foreach ($this->predictions as $gameId => $prediction) {
            if (!$this->games->contains('id', $gameId)) {
                $this->errorMessage = 'Se ha enviado un partido que no pertenece a esta jornada.';
                return;
            }

            Prediction::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'game_id' => $gameId,
                ],
                [
                    'prediction' => $prediction,
                ],
            );
        }

        $this->successMessage = 'Predicciones guardadas correctamente.';
    }
};
?>


<div>

    <div style="margin-bottom: 40px;">

        <div style="display: flex; align-items: center; gap: 12px;">

            <div style="width: 5px; height: 58px; border-radius: 999px; background: #16a34a; flex-shrink: 0;">
            </div>

            <div>

                <p
                    style="margin: 0 0 6px 0; font-size: 12px; font-weight: 800; letter-spacing: 0.25em; text-transform: uppercase; color: #2563eb;">
                    NFL Pick'em all
                </p>

                <h1
                    style="margin: 0; font-size: 40px; line-height: 1.1; font-weight: 900; letter-spacing: -0.04em; color: #111827;">
                    Predicciones · Week {{ $round->week }}
                </h1>

            </div>

        </div>

        <div style="display: flex; align-items: center; gap: 10px; margin-top: 18px; margin-left: 17px;">

            <div style="width: 32px; height: 2px; background: #123B63;">
            </div>

            <p
                style="margin: 0; font-size: 13px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6b7280;">
                Temporada {{ $round->season }} · Week {{ $round->week }}
            </p>

        </div>

        <div style="margin-top: 20px; margin-left: 17px;">

            @if ($round->status === 'open')
                <span
                    style="display: inline-flex; padding: 8px 16px; border-radius: 999px; background: #d1fae5; color: #047857; font-size: 13px; font-weight: 800;">
                    Predicciones abiertas
                </span>
            @elseif ($round->status === 'closed')
                <span
                    style="display: inline-flex; padding: 8px 16px; border-radius: 999px; background: #fef3c7; color: #b45309; font-size: 13px; font-weight: 800;">
                    Predicciones cerradas
                </span>
            @elseif ($round->status === 'finished')
                <span
                    style="display: inline-flex; padding: 8px 16px; border-radius: 999px; background: #f1f5f9; color: #475569; font-size: 13px; font-weight: 800;">
                    Jornada finalizada
                </span>
            @else
                <span
                    style="display: inline-flex; padding: 8px 16px; border-radius: 999px; background: #f1f5f9; color: #64748b; font-size: 13px; font-weight: 800;">
                    Próximamente
                </span>
            @endif

        </div>

        @if ($round->isFinished())
            <div style="margin-top: 16px; margin-left: 17px;">

                <a href="{{ route('rounds.scoreboard', $round) }}"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-red-500 transition hover:bg-slate-700">
                    Ver marcador
                </a>

            </div>
        @endif

    </div>

    @foreach ($games as $game)
        <div wire:key="game-{{ $game->id }}"
            style="display: flex; align-items: center; gap: 40px; padding: 30px 0; border-bottom: 2px solid #123B63;">

            <div style="flex: 1; display: flex; align-items: center; gap: 40px;">

                {{-- INFORMACIÓN DEL PARTIDO --}}
                <div style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 24px;">

                    <div
                        style=" min-width: 190px; text-align: right; padding-right: 20px; border-right: 3px solid #123B63;">
                        <p
                            style="margin: 0; font-size: 18px; line-height: 1; font-weight: 900; letter-spacing: -0.02em; text-transform: uppercase; color: #123B63;">
                            {{ $game->home_team }}

                            <span style="color: #8F1D2C; font-size: 13px; font-weight: 900; letter-spacing: 0.08em;">
                                · H
                            </span>
                        </p>
                    </div>

                    <div
                        style=" min-width: 90px; text-align: center; font-size: 28px; font-weight: 900; letter-spacing: -0.04em; color: #123B63;">
                        @if ($game->home_score !== null && $game->away_score !== null)
                            {{ $game->home_score }}
                            <span style="margin: 0 6px; color: #8F1D2C;">
                                -
                            </span>
                            {{ $game->away_score }}
                        @else
                            <span style="font-size: 13px; color: #9ca3af;">
                                VS
                            </span>
                        @endif
                    </div>

                    <div
                        style="min-width: 190px; text-align: left; padding-left: 20px; border-left: 3px solid #123B63;">
                        <p
                            style="margin: 0; font-size: 18px; line-height: 1; font-weight: 900; letter-spacing: -0.02em; text-transform: uppercase; color: #123B63;">
                            <span style="color: #8F1D2C; font-size: 13px; font-weight: 900; letter-spacing: 0.08em;">
                                A ·
                            </span>

                            {{ $game->away_team }}
                        </p>
                    </div>

                </div>

                {{-- INFORMACIÓN DE LA PREDICCIÓN --}}
                <div
                    style=" width: 720px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-left: 2px solid #123B63; padding-left: 40px;">

                    @if ($round->isFinished())
                        <div style=" display: flex; align-items: center; justify-content: center; gap: 80px;">

                            @foreach ($allPredictions->where('game_id', $game->id) as $prediction)
                                <div class="w-24 text-center">

                                    <p
                                        style=" margin: 0; font-size: 11px; font-weight: 900; letter-spacing: 0.12em; text-transform: uppercase; color: #123B63;">
                                        {{ $prediction->user->name }}
                                    </p>

                                    <p
                                        style="margin: 4px 0 0; font-size: 26px; line-height: 1; font-weight: 900; color: #123B63;">
                                        {{ $prediction->prediction }}
                                    </p>

                                    @if ($prediction->prediction === $game->result)
                                        <span
                                            style="display: inline-block; margin-top: 4px; font-size: 15px; font-weight: 900; color: #16a34a;">
                                            ✓
                                        </span>
                                    @else
                                        <span
                                            style="display: inline-block; margin-top: 4px; font-size: 15px; font-weight: 900; color: #dc2626;">
                                            ✗
                                        </span>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    @elseif ($round->isClosed())
                        <span class="rounded-full bg-amber-100 px-4 py-2 text-sm font-bold text-amber-700">
                            🔒 Predicciones cerradas
                        </span>
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; gap: 18px;">

                            <span
                                style="margin-right: 10px; font-size: 11px; font-weight: 900; letter-spacing: 0.14em; text-transform: uppercase; color: #6b7280;">
                                Predicción
                            </span>

                            <label
                                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #8F1D2C; border-radius: 8px; background: white;">
                                <input type="radio" wire:model="predictions.{{ $game->id }}" value="1"
                                    @disabled($status !== 'open')
                                    style="width: 18px; height: 18px; accent-color: #123B63; cursor: pointer;">

                                <span style=" font-size: 16px; font-weight: 900; color: #123B63;">
                                    1
                                </span>
                            </label>

                            <label
                                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #8F1D2C; border-radius: 8px; background: white;">
                                <input type="radio" wire:model="predictions.{{ $game->id }}" value="X"
                                    @disabled($status !== 'open')
                                    style="width: 18px; height: 18px; accent-color: #123B63; cursor: pointer;">

                                <span style="font-size: 16px; font-weight: 900; color: #123B63;">
                                    X
                                </span>
                            </label>

                            <label
                                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 12px; border: 2px solid #8F1D2C; border-radius: 8px; background: white;">
                                <input type="radio" wire:model="predictions.{{ $game->id }}" value="2"
                                    @disabled($status !== 'open')
                                    style="width: 18px; height: 18px; accent-color: #123B63; cursor: pointer;">

                                <span style="font-size: 16px; font-weight: 900; color: #123B63;">
                                    2
                                </span>
                            </label>

                        </div>
                    @endif

                </div>

            </div>

        </div>
    @endforeach


    @if ($status === 'open')
        <button type="button" wire:click="savePredictions"
            style="margin-top: 28px; padding: 13px 24px; border: 2px solid #123B63; border-radius: 10px; background: #123B63; color: white; font-size: 14px; font-weight: 900; letter-spacing: 0.04em; cursor: pointer; transition: all 0.2s ease;"
            onmouseover="this.style.background='#8F1D2C'; this.style.borderColor='#8F1D2C';"
            onmouseout="this.style.background='#123B63'; this.style.borderColor='#123B63';">
            Guardar predicciones
        </button>
        @if ($successMessage)
            <p class="mt-4 text-sm font-bold text-green-600">
                {{ $successMessage }}
            </p>
        @endif

        @if ($errorMessage)
            <p class="mt-4 text-sm font-bold text-red-600">
                {{ $errorMessage }}
            </p>
        @endif
    @endif

</div>
