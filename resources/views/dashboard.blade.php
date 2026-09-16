@php

    $round = \App\Models\Round::where('status', 'open')->orderBy('starts_at')->first();

    $nextRound = \App\Models\Round::where('status', 'upcoming')->orderBy('starts_at')->first();

    $closedRound = \App\Models\Round::where('status', 'closed')->orderBy('starts_at')->first();

    $lastRound = \App\Models\Round::where('status', 'finished')->orderByDesc('starts_at')->first();

    $lastResult = $lastRound
        ? $lastRound
            ->roundResults()
            ->where('user_id', auth()->id())
            ->first()
        : null;

    $currentSeason = \App\Models\Round::orderByDesc('starts_at')->value('season');

    $standings = \App\Models\RoundResult::query()
        ->with('user')
        ->whereHas('round', function ($query) use ($currentSeason) {
            $query->where('season', $currentSeason);
        })
        ->selectRaw('user_id, SUM(points) as total_points')
        ->groupBy('user_id')
        ->orderByDesc('total_points')
        ->get();

    $games = $round ? $round->games : collect();
    $predictionCount = $round ? auth()->user()->predictions()->whereIn('game_id', $games->pluck('id'))->count() : 0;

@endphp

<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl" style="background-color: white;">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="dashboard-card relative flex h-full flex-col overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">

                @if ($round)
                    <p class="text-sm font-medium text-white">
                        Temporada {{ $round->season }}
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        Week {{ $round->week }}
                    </h2>

                    <p class="mt-2 text-sm text-white">
                        Predicciones abiertas
                    </p>

                    <p class="mt-2 text-sm font-medium text-white">
                        {{ $predictionCount }} de {{ $games->count() }} predicciones realizadas
                    </p>
                    <a href="{{ route('rounds.predictions', $round) }}"
                        class="mt-auto inline-block w-fit rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#3B7A57] hover:bg-gray-100">
                        @if ($predictionCount === $games->count())
                            Modificar predicciones
                        @else
                            Hacer predicciones
                        @endif
                    </a>
                @elseif ($nextRound)
                    <p class="text-sm font-medium text-white">
                        Próxima jornada
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        Week {{ $nextRound->week }}
                    </h2>

                    <p class="mt-2 text-sm text-white">
                        Las predicciones todavía no están abiertas.
                    </p>
                @elseif ($closedRound)
                    <p class="text-sm font-medium text-white">
                        Jornada en curso
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        Week {{ $closedRound->week }}
                    </h2>

                    <p class="mt-2 text-sm text-white">
                        Las predicciones están cerradas y la jornada está en curso.
                    </p>
                    <a href="{{ route('rounds.scoreboard', $closedRound) }}"
                        class="mt-4 inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                        Ver jornada </a>
                @else
                    <p class="text-sm font-medium text-white">
                        NFL Pickem all
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-white">
                        No hay jornadas próximas
                    </h2>

                    <p class="mt-2 text-sm text-white">
                        Actualmente no hay ninguna jornada disponible.
                    </p>
                @endif
            </div>

            <div
                class="relative flex h-full flex-col overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 dashboard-card">

                <p class="text-sm font-medium text-white">
                    Última jornada
                </p>

                {{-- <h2 class="mt-1 text-2xl font-bold text-white">
                    Week {{ $lastRound->week }}
                </h2> --}}
                <h2 class="mt-1 text-2xl font-bold text-white">
                    @if ($lastRound)
                        Week {{ $lastRound->week }}
                    @else
                        Sin jornadas
                    @endif
                </h2>

                @if ($lastResult)
                    <p class="mt-4 text-3xl font-black text-white">
                        {{ $lastResult->correct_predictions }}
                        <span class="text-lg font-semibold">
                            {{ $lastResult->correct_predictions === 1 ? 'acierto' : 'aciertos' }}
                        </span>
                    </p>

                    <p class="mt-1 text-sm font-medium text-white">
                        {{ $lastResult->points }}
                        {{ $lastResult->points === 1 ? 'punto' : 'puntos' }}
                    </p>

                    <a href="{{ route('rounds.scoreboard', $lastRound) }}"
                        class="mt-auto inline-block w-fit rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#123B63]">
                        Ver resultados
                    </a>
                @else
                    <p class="mt-4 text-sm text-white">
                        Todavía no hay resultados.
                    </p>
                @endif
            </div>

            <div
                class="relative flex h-full flex-col overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 dashboard-card">

                <p class="text-sm font-medium text-white">
                    Clasificación
                </p>

                {{-- <h2 class="mt-1 text-2xl font-bold text-white">
                    Temporada {{ $lastRound->season }}
                </h2> --}}
                <h2 class="mt-1 text-2xl font-bold text-white">
                    @if ($lastRound)
                        Temporada {{ $lastRound->season }}
                    @else
                        Sin temporada
                    @endif
                </h2>

                <div class="mt-5 space-y-2">

                    @foreach ($standings->take(3) as $position => $standing)
                        <div class="flex items-center justify-between rounded-lg bg-white/10 px-3 py-2">

                            <p class="font-semibold text-white">
                                {{ $position + 1 }}. {{ $standing->user->name }}
                            </p>

                            <p class="text-sm font-medium text-white">
                                {{ $standing->total_points }}
                                {{ $standing->total_points == 1 ? 'punto' : 'puntos' }}
                            </p>

                        </div>
                    @endforeach

                </div>

                <a href="{{ route('standings') }}"
                    class="mt-auto inline-block w-fit rounded-lg bg-white px-4 py-2 text-sm font-bold text-[#123B63] hover:bg-gray-100">
                    Ver clasificación
                </a>

            </div>
        </div>
        <div
            class="dashboard-card relative flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-6">
            <div>

                <h2 class="text-xl font-bold text-white">
                    @if ($round)
                        Week {{ $round->week }} · Partidos
                    @else
                        Jornada actual
                    @endif
                </h2>

                @if ($round)
                    <p class="mt-1 text-sm text-white">
                        Temporada {{ $round->season }}
                    </p>
                @endif
            </div>

            <div style="margin-top: 24px;">

                @if ($games->isEmpty())

                    <p
                        style="
                color: rgba(255,255,255,0.8);
                font-size: 14px;
                font-weight: 600;
            ">
                        No hay ninguna jornada abierta actualmente.
                    </p>
                @else
                    @foreach ($games as $game)
                        <div
                            style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 70px;
                    border-top: {{ $loop->first ? '2px solid #123B63' : '1px solid rgba(255,255,255,0.15)' }};
                    border-bottom: {{ $loop->last ? '2px solid #123B63' : 'none' }};
                ">

                            {{-- EQUIPO LOCAL --}}
                            <div
                                style="
                        flex: 1;
                        text-align: right;
                        padding-right: 24px;
                    ">
                                <span
                                    style="
                            font-size: 16px;
                            font-weight: 900;
                            letter-spacing: -0.01em;
                            text-transform: uppercase;
                            color: white;
                        ">
                                    {{ $game->home_team }}

                                    <span
                                        style="
                                color: #D62027;
                                font-size: 11px;
                                font-weight: 900;
                                letter-spacing: 0.08em;
                            ">
                                        · H
                                    </span>
                                </span>
                            </div>

                            {{-- RESULTADO / VS --}}
                            <div
                                style="
                        width: 80px;
                        flex-shrink: 0;
                        text-align: center;
                        font-size: 13px;
                        font-weight: 900;
                        letter-spacing: 0.12em;
                        color: #D62027;
                    ">
                                @if ($game->home_score !== null && $game->away_score !== null)
                                    <span style="color: white; font-size: 18px;">
                                        {{ $game->home_score }}
                                    </span>

                                    <span style="margin: 0 5px;">
                                        -
                                    </span>

                                    <span style="color: white; font-size: 18px;">
                                        {{ $game->away_score }}
                                    </span>
                                @else
                                    VS
                                @endif
                            </div>

                            {{-- EQUIPO VISITANTE --}}
                            <div
                                style="
                        flex: 1;
                        text-align: left;
                        padding-left: 24px;
                    ">
                                <span
                                    style="
                            font-size: 16px;
                            font-weight: 900;
                            letter-spacing: -0.01em;
                            text-transform: uppercase;
                            color: white;
                        ">
                                    {{ $game->away_team }}

                                    <span
                                        style="
                                color: #D62027;
                                font-size: 11px;
                                font-weight: 900;
                                letter-spacing: 0.08em;
                            ">
                                        · A
                                    </span>
                                </span>
                            </div>

                        </div>
                    @endforeach

                @endif

            </div>
        </div>
    </div>
</x-layouts::app>
