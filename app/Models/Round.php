<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Round extends Model
{
    protected $fillable = [
        'season',
        'week',
        'starts_at',
        'prediction_deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'prediction_deadline' => 'datetime',
        ];
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function roundResults()
    {
        return $this->hasMany(RoundResult::class);
    }

    public function correctPredictionsForUser($user): int
    {
        return $this->games
            ->flatMap(fn ($game) => $game->predictions)
            ->filter(fn ($prediction) => $prediction->user_id === $user->id)
            ->filter(fn ($prediction) => $prediction->isCorrect())
            ->count();
    }
    public function resultsForUsers()
    {
        return User::all()->map(function ($user) {
            return [
                'user' => $user,
                'correct' => $this->correctPredictionsForUser($user),
            ];
        });
    }

    public function calculatePoints()
    {
        $results = $this->resultsForUsers();

        $maxCorrect = $results->max('correct');

        if ($maxCorrect === 0) {
            return $results->map(function ($result) {
                return [
                    'user' => $result['user'],
                    'correct' => $result['correct'],
                    'points' => 0,
                ];
            });
        }

        return $results->map(function ($result) use ($maxCorrect) {
            return [
                'user' => $result['user'],
                'correct' => $result['correct'],
                'points' => $result['correct'] === $maxCorrect ? 1 : 0,
            ];
        });
    }

    public function predictionsAreOpen(): bool
    {
        return $this->status === 'open'
            && now()->lessThanOrEqualTo($this->prediction_deadline);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function saveResults()
    {
        $results = $this->calculatePoints();

        foreach ($results as $result) {
            RoundResult::updateOrCreate(
                [
                    'round_id' => $this->id,
                    'user_id' => $result['user']->id,
                ],
                [
                    'correct_predictions' => $result['correct'],
                    'points' => $result['points'],
                ],
            );
        }
    }
}
