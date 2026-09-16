<?php

use Livewire\Component;

new class extends Component {
    public $season = '2026-2027';
    public $week = '';
    public $startsAt = '';
    public $predictionDeadline = '';
    public $successMessage = '';

    public function mount()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
    }

    public function createRound()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $this->validate([
            'season' => 'required|string|max:20',
            'week' => 'required|integer|min:1|max:22',
            'startsAt' => 'required|date',
            'predictionDeadline' => 'required|date|before:startsAt',
        ]);

        if (\App\Models\Round::where('season', $this->season)->where('week', $this->week)->exists()) {
            $this->addError('week', 'Esta Week ya existe para esta temporada.');
            return;
        }

        \App\Models\Round::create([
            'season' => $this->season,
            'week' => $this->week,
            'starts_at' => $this->startsAt,
            'prediction_deadline' => $this->predictionDeadline,
            'status' => 'upcoming',
        ]);

        $this->successMessage = 'Jornada creada correctamente.';
    }
};
?>

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold">
            Nueva jornada
        </h1>

        <p class="mt-1 text-sm text-gray-600">
            Crea una nueva jornada de la temporada.
        </p>
        @if ($errors->any())
            <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($successMessage)
            <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-sm text-green-700">
                {{ $successMessage }}
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-neutral-200 p-5">

        <div class="grid gap-4 md:grid-cols-2">

            <div>
                <label class="block text-sm font-medium">
                    Temporada
                </label>

                <input type="text" wire:model="season" class="mt-1 w-full rounded-lg border px-3 py-2"
                    placeholder="Ej. 2026-2027">
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Week
                </label>

                <input type="number" wire:model="week" class="mt-1 w-full rounded-lg border px-3 py-2"
                    placeholder="Ej. 7">
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Inicio de la jornada
                </label>

                <input type="datetime-local" wire:model="startsAt" class="mt-1 w-full rounded-lg border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">
                    Límite de predicciones
                </label>

                <input type="datetime-local" wire:model="predictionDeadline"
                    class="mt-1 w-full rounded-lg border px-3 py-2">
            </div>

        </div>

        <button type="button" wire:click="createRound"
            class="mt-6 rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white">
            Crear jornada
        </button>

    </div>

</div>
