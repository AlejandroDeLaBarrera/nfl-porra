<?php

use Livewire\Component;
use App\Models\Round;

new class extends Component {
    public Round $round;
    public $games;

    public $homeTeam = '';
    public $awayTeam = '';

    public $editingGameId = null;
    public $editHomeTeam = '';
    public $editAwayTeam = '';

    public function mount()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->games = $this->round->games;
    }

    public function addGame()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (!$this->round->isUpcoming() && !$this->round->isOpen()) {
            return;
        }

        $this->validate([
            'homeTeam' => 'required|string|max:255',
            'awayTeam' => 'required|string|max:255',
        ]);

        if (strtolower(trim($this->homeTeam)) === strtolower(trim($this->awayTeam))) {
            $this->addError('awayTeam', 'El equipo local y el visitante no pueden ser el mismo.');
            return;
        }

        $this->round->games()->create([
            'home_team' => $this->homeTeam,
            'away_team' => $this->awayTeam,
        ]);

        $this->games = $this->round->games()->get();

        $this->homeTeam = '';
        $this->awayTeam = '';
    }

    public function deleteGame($gameId)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (!$this->round->isUpcoming()) {
            return;
        }

        $game = $this->round->games()->findOrFail($gameId);

        $game->delete();

        $this->games = $this->round->games()->get();
    }

    public function editGame($gameId)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (!$this->round->isUpcoming() && !$this->round->isOpen()) {
            return;
        }

        $game = $this->round->games()->findOrFail($gameId);

        $this->editingGameId = $game->id;
        $this->editHomeTeam = $game->home_team;
        $this->editAwayTeam = $game->away_team;
    }

    public function updateGame()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (!$this->round->isUpcoming() && !$this->round->isOpen()) {
            return;
        }

        $this->validate([
            'editHomeTeam' => 'required|string|max:255',
            'editAwayTeam' => 'required|string|max:255',
        ]);

        if (strtolower(trim($this->editHomeTeam)) === strtolower(trim($this->editAwayTeam))) {
            $this->addError('editAwayTeam', 'El equipo local y el visitante no pueden ser el mismo.');
            return;
        }

        $game = $this->round->games()->findOrFail($this->editingGameId);

        $game->update([
            'home_team' => $this->editHomeTeam,
            'away_team' => $this->editAwayTeam,
        ]);

        $this->games = $this->round->games()->get();

        $this->editingGameId = null;
        $this->editHomeTeam = '';
        $this->editAwayTeam = '';
    }
};
?>

<div class="space-y-6">

    <div>
        <p class="text-sm text-gray-500">
            Temporada {{ $round->season }}
        </p>

        <h1 class="mt-1 text-2xl font-bold">
            Week {{ $round->week }}
        </h1>

        <p class="mt-2 text-sm text-gray-600">
            Estado:
            @if ($round->status === 'upcoming')
                Próxima
            @elseif ($round->status === 'open')
                Abierta
            @elseif ($round->status === 'closed')
                Cerrada
            @elseif ($round->status === 'finished')
                Finalizada
            @endif
        </p>
    </div>

    <div class="rounded-xl border border-neutral-200 p-5">

        <h2 class="text-lg font-bold">
            Información de la jornada
        </h2>

        <div class="mt-4 space-y-2 text-sm text-gray-600">

            <p>
                Inicio:
                {{ $round->starts_at->format('d/m/Y H:i') }}
            </p>

            <p>
                Límite de predicciones:
                {{ $round->prediction_deadline->format('d/m/Y H:i') }}
            </p>

        </div>

    </div>

    <div class="rounded-xl border border-neutral-200 p-5">

        <h2 class="text-lg font-bold">
            Partidos
        </h2>

        {{-- @if ($round->status === 'upcoming')
            <form wire:submit="addGame" class="mt-4"> --}}
        @if ($round->status === 'upcoming' || $round->status === 'open')
            <form wire:submit="addGame" class="mt-4">

                <div class="grid gap-4 md:grid-cols-2">

                    <div>
                        <label class="block text-sm font-medium">
                            Equipo local
                        </label>

                        <input type="text" wire:model="homeTeam" class="mt-1 w-full rounded-lg border px-3 py-2"
                            placeholder="Ej. Eagles">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">
                            Equipo visitante
                        </label>

                        <input type="text" wire:model="awayTeam" class="mt-1 w-full rounded-lg border px-3 py-2"
                            placeholder="Ej. Cowboys">

                        @error('awayTeam')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="mt-4 rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                    Añadir partido
                </button>

            </form>
        @endif

        <div class="mt-4 space-y-3">

            @if ($games->isEmpty())

                <p class="text-sm text-gray-600">
                    Esta jornada todavía no tiene partidos.
                </p>
            @else
                @foreach ($games as $game)
                    <div class="flex items-center justify-between rounded-lg border p-4">

                        <div>
                            <span class="font-semibold">
                                {{ $game->home_team }}
                            </span>

                            <span class="mx-2 text-gray-500">
                                vs
                            </span>

                            <span class="font-semibold">
                                {{ $game->away_team }}
                            </span>
                        </div>

                        @if ($editingGameId === $game->id)
                            <div class="mt-3 rounded-lg border border-neutral-200 bg-gray-50 p-4">

                                <div class="grid gap-4 md:grid-cols-2">

                                    <div>
                                        <label class="block text-sm font-medium">
                                            Equipo local
                                        </label>

                                        <input type="text" wire:model="editHomeTeam"
                                            class="mt-1 w-full rounded-lg border px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium">
                                            Equipo visitante
                                        </label>

                                        <input type="text" wire:model="editAwayTeam"
                                            class="mt-1 w-full rounded-lg border px-3 py-2">

                                        @error('editAwayTeam')
                                            <p class="mt-1 text-sm text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                </div>

                                <div class="mt-4 flex gap-3">

                                    <button type="button" wire:click="updateGame"
                                        class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                                        Guardar cambios
                                    </button>

                                    <button type="button" wire:click="$set('editingGameId', null)"
                                        class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold">
                                        Cancelar
                                    </button>

                                </div>

                            </div>
                        @endif

                        <div class="text-sm text-gray-600">

                            @if ($game->home_score !== null && $game->away_score !== null)
                                {{ $game->home_score }} - {{ $game->away_score }}
                            @else
                                Pendiente
                            @endif

                        </div>

                        @if ($round->status === 'upcoming' || $round->status === 'open')
                            <button type="button" wire:click="editGame({{ $game->id }})"
                                class="rounded-lg bg-gray-200 px-3 py-2 text-sm font-semibold">
                                Editar
                            </button>
                        @endif

                        @if ($round->status === 'upcoming')
                            <button type="button" wire:click="deleteGame({{ $game->id }})"
                                class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white">
                                Eliminar
                            </button>
                        @endif

                    </div>
                @endforeach

            @endif

        </div>

    </div>

    @if ($round->status === 'closed')
        <div class="rounded-xl border border-neutral-200 p-5">
            <h2 class="text-lg font-bold">
                Resultados
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Introduce los resultados de los partidos para finalizar la jornada.
            </p>

            <a href="{{ route('rounds.results', $round) }}"
                class="mt-4 inline-block rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
                Gestionar resultados
            </a>
        </div>
    @endif

</div>
