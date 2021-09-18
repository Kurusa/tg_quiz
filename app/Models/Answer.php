<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{

    protected $table = 'answer';
    protected $fillable = ['user_id', 'game_id', 'answer'];

}