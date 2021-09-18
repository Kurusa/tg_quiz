<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{

    protected $table = 'score';
    protected $fillable = ['user_id', 'game_id', 'score', 'wrong', 'lifes', 'guess', 'status'];

}