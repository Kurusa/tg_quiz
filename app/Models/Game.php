<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    protected $table = 'game_list';
    protected $fillable = ['title', 'api_path', 'trigger_class', 'lives'];
    const UPDATED_AT = false;

}