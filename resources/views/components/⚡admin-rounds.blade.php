<?php

use Livewire\Component;
use App\Models\Round;

new class extends Component {
    public $rounds;
    public $errorMessage = '';
    public $successMessage = '';

    public function mount()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->rounds = Round::orderBy('starts_at')->get();
    }

    public function openRound($roundId)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->errorMessage = '';
        $this->successMessage = '';

        $round = Round::findOrFail($roundId);

        if (!$round->isUpcoming()) {
            return;
        }

        if ($round->games()->count() === 0) {
            $this->errorMessage = 'No puedes abrir una jornada sin partidos.';
            return;
        }

        $round->update([
            'status' => 'open',
        ]);

        $this->successMessage = "Week {$round->week} abierta correctamente.";

        $this->rounds = Round::orderBy('starts_at')->get();
    }

    public function closeRound($roundId)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->errorMessage = '';
        $this->successMessage = '';

        $round = Round::findOrFail($roundId);

        if (!$round->isOpen()) {
            return;
        }

        $round->status = 'closed';
        $round->save();

        $this->successMessage = "Week {$round->week} cerrada correctamente.";

        $this->rounds = Round::orderBy('starts_at')->get();
    }
};
?>

<div class="space-y-6">

    {{-- <div>
        <h1
            style="
        margin: 0;
        font-size: 32px;
        line-height: 1.1;
        font-weight: 900;
        letter-spacing: -0.03em;
        color: #123B63 !important;
    ">
            Administración de jornadas
        </h1>

        <p class="mt-1 text-sm text-gray-600">
            Gestiona las jornadas de la temporada.
        </p>

        <a href="{{ route('admin.rounds.create') }}"
            class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
            + Crear jornada
        </a>
    </div> --}}
    <div style=" margin-bottom: 32px; padding-left: 16px; border-left: 5px solid #8F1D2C; ">
        <p
            style=" margin: 0; font-size: 12px; font-weight: 900; letter-spacing: 0.18em; text-transform: uppercase; color: #8F1D2C; ">
            NFL Pick'em all </p>

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
            Administración de jornadas
        </h1>

        <p
            style="
        margin-top: 8px;
        margin-bottom: 16px;
        font-size: 13px;
        font-weight: 700;
        color: #64748B !important;
    ">
            Gestiona las jornadas de la temporada.
        </p>

        <a href="{{ route('admin.rounds.create') }}"
            class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
            + Crear jornada
        </a>

    </div>


    @if ($errorMessage)
        <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
            {{ $errorMessage }}
        </div>
    @endif

    @if ($successMessage)
        <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-700">
            {{ $successMessage }}
        </div>
    @endif

    <div class="space-y-3">

        @foreach ($rounds as $round)
            <div class="rounded-xl border border-neutral-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Temporada {{ $round->season }}
                        </p>

                        <h2 class="text-xl font-bold">
                            Week {{ $round->week }}
                        </h2>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-sm">
                        @if ($round->status === 'upcoming')
                            Próxima
                        @elseif ($round->status === 'open')
                            Abierta
                        @elseif ($round->status === 'closed')
                            Cerrada
                        @elseif ($round->status === 'finished')
                            Finalizada
                        @endif
                    </span>

                    @if ($round->status === 'upcoming')
                        <button type="button" wire:click="openRound({{ $round->id }})"
                            class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                            Abrir jornada
                        </button>
                    @endif

                    @if ($round->status === 'open')
                        <button type="button" wire:click="closeRound({{ $round->id }})"
                            class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                            Cerrar jornada
                        </button>
                    @endif

                    <a href="{{ route('admin.round', $round) }}"
                        class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                        Gestionar
                    </a>

                </div>

            </div>
        @endforeach

    </div>

</div>
