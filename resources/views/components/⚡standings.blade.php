<?php

use Livewire\Component;
use App\Models\RoundResult;

new class extends Component {
    public $standings;

    public function mount()
    {
        $currentSeason = \App\Models\Round::orderByDesc('starts_at')->value('season');

        $this->standings = RoundResult::query()
            ->with('user')
            ->whereHas('round', function ($query) use ($currentSeason) {
                $query->where('season', $currentSeason);
            })
            ->selectRaw('user_id, SUM(points) as total_points')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();
    }
};
?>

<div class="mx-auto w-full max-w-lg">

    {{-- Cabecera --}}
    <div style="margin-bottom: 120px; margin-top: 70px;">

        <div style="display: flex; align-items: center; gap: 12px;">

            <div
                style="
                width: 5px;
                height: 58px;
                border-radius: 999px;
                background: #8F1D2C;
                flex-shrink: 0;
            ">
            </div>

            <div>

                <div
                    style="
                    font-size: 12px;
                    font-weight: 800;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #8F1D2C;
                    margin-bottom: 6px;
                ">
                    NFL Pick'em All
                </div>

                <h1
                    style="
                    margin: 0;
                    font-size: 42px;
                    line-height: 1;
                    font-weight: 900;
                    letter-spacing: -0.04em;
                    color: #111827;
                ">
                    Clasificación General
                </h1>

            </div>

        </div>


        <div
            style="
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
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
                Temporada 2026-2027
            </p>

        </div>

    </div>


    {{-- Clasificación --}}
    <div class="mt-10 mx-auto w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-sm"
        style="border: 2px solid #123B63;">

        {{-- Cabecera de la tabla --}}
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-6"
            style="
        padding-top: 18px;
        padding-bottom: 18px;
        border-bottom: 2px solid #123B63;
    ">

            <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">
                Jugador
            </p>

            <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">
                Puntos
            </p>

        </div>


        @php
            $position = 0;
            $previousPoints = null;
        @endphp


        @foreach ($standings as $index => $standing)
            @if ($previousPoints !== $standing->total_points)
                @php
                    $position = $index + 1;
                @endphp
            @endif


            <div class="flex items-center justify-between px-6 transition hover:bg-slate-50"
                style="
        padding-top: 25px;
        padding-bottom: 25px;
        {{ !$loop->last ? 'border-bottom: 2px solid #123B63;' : '' }}
    ">

                {{-- Posición + jugador --}}
                <div class="flex items-center gap-4">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center">

                        @if ($position === 1)
                            <span class="text-2xl">
                                🥇
                            </span>
                        @elseif ($position === 2)
                            <span class="text-2xl">
                                🥈
                            </span>
                        @elseif ($position === 3)
                            <span class="text-2xl">
                                🥉
                            </span>
                        @else
                            <span class="text-lg font-black text-slate-400">
                                {{ $position }}
                            </span>
                        @endif

                    </div>


                    <div>

                        <p class="font-bold text-slate-900">
                            {{ $standing->user->name }}
                        </p>

                        @if ($position === 1)
                            <p class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-emerald-600">
                                Líder
                            </p>
                        @endif

                    </div>

                </div>


                {{-- Puntos --}}
                <div class="text-right">

                    <p
                        style="
        margin: 0;
        font-size: 28px;
        line-height: 1;
        font-weight: 900;
        color: {{ $position === 1 ? '#16a34a' : '#111827' }};
    ">
                        {{ $standing->total_points }}
                    </p>

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        {{ $standing->total_points == 1 ? 'punto' : 'puntos' }}
                    </p>

                </div>

            </div>


            @php
                $previousPoints = $standing->total_points;
            @endphp
        @endforeach

    </div>

</div>
