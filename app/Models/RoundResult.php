<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoundResult extends Model
{
    protected $fillable = [
        'round_id',
        'user_id',
        'correct_predictions',
        'points',
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}