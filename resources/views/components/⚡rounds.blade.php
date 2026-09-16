<?php

use Livewire\Component;
use App\Models\Round;

new class extends Component {
    public $rounds;

    public function mount()
    {
        $currentSeason = Round::orderByDesc('starts_at')->value('season');

        $this->rounds = Round::where('season', $currentSeason)
            ->with([
                'roundResults' => function ($query) {
                    $query->where('user_id', auth()->id());
                },
            ])
            ->orderByDesc('week')
            ->get();
    }
};
?>

<div class="mx-auto max-w-5xl">

    {{-- <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-[0.2em] text-blue-600">
            NFL Pick'em all
        </p>

        <h1 class="mt-2 text-4xl font-black tracking-tight" style="color: #123B63 !important;">
            Jornadas
        </h1>

        @if ($rounds->isNotEmpty())
            <p class="mt-2 text-sm font-medium text-slate-500">
                Temporada {{ $rounds->first()->season }}
            </p>
        @endif
    </div> --}}
    <div style="
        margin-bottom: 32px;
        padding-left: 16px;
        border-left: 5px solid #8F1D2C;
    ">
        <p
            style="
            margin: 0;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #8F1D2C;
        ">
            NFL Pick'em all
        </p>

        <h1
            style="
            margin-top: 6px;
            margin-bottom: 0;
            font-size: 32px;
            line-height: 1.1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #123B63 !important;
        ">
            Jornadas
        </h1>

        @if ($rounds->isNotEmpty())
            <p
                style="
                margin-top: 8px;
                margin-bottom: 0;
                font-size: 13px;
                font-weight: 700;
                color: #64748B !important;
            ">
                Temporada {{ $rounds->first()->season }}
            </p>
        @endif
    </div>

    <div class="grid gap-4">

        @foreach ($rounds as $round)
            <a href="{{ route('rounds.predictions', $round) }}" wire:navigate
                class="group block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">

                <div class="flex items-center justify-between gap-4">

                    <div style="display: flex; align-items: center; gap: 16px;">

                        <div
                            style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 56px;
                                    height: 56px;
                                    flex-shrink: 0;
                                    border-radius: 12px;
                                    background: #123B63;
                                    color: white;
                                    font-size: 18px;
                                    font-weight: 900;
                            ">
                            {{ $round->week }}
                        </div>

                        <div>

                            <p
                                style="
                                        margin: 0 0 4px;
                                        font-size: 11px;
                                        font-weight: 800;
                                        letter-spacing: 0.15em;
                                        text-transform: uppercase;
                                        color: #8F1D2C;
                                ">
                                Week
                            </p>

                            <h2
                                style="
                                        margin: 0;
                                        font-size: 20px;
                                        font-weight: 900;
                                        color: #111827;
                                ">
                                Jornada {{ $round->week }}
                            </h2>

                        </div>

                    </div>


                    <div class="text-right">

                        @if ($round->isFinished())
                            <span
                                class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                Finalizada
                            </span>
                        @elseif ($round->isClosed())
                            <span
                                class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                Cerrada
                            </span>
                        @elseif ($round->isOpen())
                            <span
                                class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                Abierta
                            </span>
                        @else
                            <span
                                class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                Próximamente
                            </span>
                        @endif

                    </div>

                </div>


                @if ($round->isFinished() && $round->roundResults->isNotEmpty())
                    <div class="mt-5 flex items-center gap-6 border-t border-slate-100 pt-4">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Tus aciertos
                            </p>

                            <p class="mt-1 text-lg font-black text-slate-900">
                                {{ $round->roundResults->first()->correct_predictions }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Puntos
                            </p>

                            <p class="mt-1 text-lg font-black text-blue-600">
                                {{ $round->roundResults->first()->points }}
                            </p>
                        </div>

                    </div>
                @endif


                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">

                    <p class="text-sm font-medium text-slate-400">
                        Ver jornada
                    </p>

                    <span
                        class="text-lg font-bold text-slate-400 transition group-hover:translate-x-1 group-hover:text-slate-700">
                        →
                    </span>

                </div>

            </a>
        @endforeach

    </div>

</div>
