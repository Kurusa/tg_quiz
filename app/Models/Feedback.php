<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $table = 'feedback';
    protected $fillable = ['user_id', 'text'];
    const UPDATED_AT = null;

}