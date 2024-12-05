<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameState extends Model
{
    protected $fillable = [
        'user_name',
        'grid_size',
        'grid_state',
        'random_number',
        'treasures_found',
        'misses',
        'completed'
    ];

    protected $casts = [
        'grid_state' => 'array',
        'completed' => 'boolean'
    ];
}
