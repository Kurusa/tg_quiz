<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'user';
    protected $fillable = ['chat_id', 'first_name', 'user_name', 'game_entity_id', 'status'];

    public function game_entity()
    {
        return $this->hasOne(GameEntity::class);
    }

}