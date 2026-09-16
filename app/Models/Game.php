<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
     protected $fillable = [
        'round_id',
        'home_team',
        'away_team',
        'home_score',
        'away_score',
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    public function getResultAttribute()
    {
        if ($this->home_score === null || $this->away_score === null) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return '1';
        }

        if ($this->home_score < $this->away_score) {
            return '2';
        }

        return 'X';
    }
}
