<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameEntity extends Model
{

    protected $table = 'game_entity';
    protected $fillable = ['user_id', 'game_id', 'score', 'wrong', 'guess', 'answer', 'status'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}