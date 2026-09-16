<?php

use Livewire\Component;
use App\Models\Round;

new class extends Component {
    public Round $round;
    public $results;

    public function mount()
    {
        $this->results = $this->round
            ->roundResults()
            ->with([
                'user',
                'user.predictions' => function ($query) {
                    $query->whereIn('game_id', $this->round->games->pluck('id'));
                },
                'user.predictions.game',
            ])
            ->get();
    }
};
?>

<div class="space-y-6">

    <div style="margin-bottom: 40px;">

        <div style="display: flex; align-items: center; gap: 12px;">

            <div
                style="
                width: 5px;
                height: 58px;
                border-radius: 999px;
                background: #16a34a;
                flex-shrink: 0;
            ">
            </div>

            <div>

                <p
                    style="
                    margin: 0 0 6px 0;
                    font-size: 12px;
                    font-weight: 800;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #2563eb;
                ">
                    NFL Pick'em
                </p>

                <h1
                    style="
                    margin: 0;
                    font-size: 40px;
                    line-height: 1.1;
                    font-weight: 900;
                    letter-spacing: -0.04em;
                    color: #111827;
                ">
                    Resultados de la jornada
                </h1>

            </div>

        </div>


        <div
            style="
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 18px;
            margin-left: 17px;
        ">

            <div
                style="
                width: 32px;
                height: 2px;
                background: #d1d5db;
            ">
            </div>

            <p
                style="
                margin: 0;
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: #6b7280;
            ">
                Temporada {{ $round->season }} · Week {{ $round->week }}
            </p>

        </div>

    </div>

    @php
        $maxPoints = $results->max('points');
        $winners = $results->where('points', $maxPoints);
    @endphp

    @if ($maxPoints === 1)
        <div
            style="
            margin-bottom: 24px;
            padding: 22px 24px;
            border: 1px solid #d1fae5;
            border-radius: 16px;
            background: #f0fdf4;
        ">

            <div
                style="
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 12px;
            ">
                <span style="font-size: 22px;">
                    🏆
                </span>

                <h2
                    style="
                    margin: 0;
                    font-size: 16px;
                    font-weight: 800;
                    color: #166534;
                ">
                    Ganador{{ $winners->count() > 1 ? 'es' : '' }} de la jornada
                </h2>
            </div>

            <div style="margin-left: 34px;">

                @foreach ($winners as $winner)
                    <p
                        style="
                        margin: 0;
                        font-size: 18px;
                        font-weight: 800;
                        color: #111827;
                    ">
                        {{ $winner->user->name }}
                    </p>
                @endforeach

            </div>

        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px;">
        @foreach ($results as $result)
            <div class="rounded-xl border p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <h2 class="text-xl font-bold">
                            @if ($result->points === 1)
                                🏆
                            @endif

                            {{ $result->user->name }}
                        </h2>
                    </div>

                    <div class="flex gap-6 text-center">

                        <div>
                            <p class="text-2xl font-bold">
                                {{ $result->correct_predictions }}
                            </p>

                            <p class="text-sm text-gray-600">
                                {{ $result->correct_predictions === 1 ? 'acierto' : 'aciertos' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-2xl font-bold">
                                {{ $result->points }}
                            </p>

                            <p class="text-sm text-gray-600">
                                {{ $result->points === 1 ? 'punto' : 'puntos' }}
                            </p>
                        </div>

                    </div>

                </div>

                <div class="mt-6 space-y-4">

                    @foreach ($result->user->predictions as $prediction)
                        @if ($prediction->isCorrect())
                            <div class="rounded-lg p-4 bg-green-200 border border-green-500">
                            @else
                                <div class="rounded-lg p-4 bg-red-200 border border-red-500">
                        @endif


                        <div class="flex items-center justify-between">

                            <div>
                                <p
                                    style="
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #111827;
    ">
                                    {{ $prediction->game->home_team }}

                                    <span
                                        style="
            display: inline-block;
            min-width: 28px;
            margin-left: 10px;
            font-size: 18px;
            font-weight: 900;
            text-align: right;
            color: #111827;
        ">
                                        {{ $prediction->game->home_score }}
                                    </span>
                                </p>

                                <p
                                    style="
        margin: 6px 0 0;
        font-size: 15px;
        font-weight: 800;
        color: #111827;
    ">
                                    {{ $prediction->game->away_team }}

                                    <span
                                        style="
            display: inline-block;
            min-width: 28px;
            margin-left: 10px;
            font-size: 18px;
            font-weight: 900;
            text-align: right;
            color: #111827;
        ">
                                        {{ $prediction->game->away_score }}
                                    </span>
                                </p>
                            </div>

                            <div class="text-right">

                                <p>
                                    Resultado:
                                    <strong>
                                        {{ $prediction->game->result }}
                                    </strong>
                                </p>

                                <p class="mt-2">
                                    Tu predicción:

                                    <span
                                        class="ml-1 inline-flex items-center rounded-full px-3 py-1 text-sm font-bold
                                        @if ($prediction->isCorrect()) bg-green-500 text-white
                                        @else bg-red-500 text-white @endif">
                                        {{ $prediction->prediction }}
                                    </span>

                                    <span class="ml-1">
                                        @if ($prediction->isCorrect())
                                            ✅
                                        @else
                                            ❌
                                        @endif
                                    </span>
                                </p>

                            </div>

                        </div>

                </div>
        @endforeach

    </div>

</div>
@endforeach

</div>
