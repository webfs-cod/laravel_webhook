<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public function survey()
    {
        return $this->belongsTo('App\Models\Survey');
    }

    public function answers()
    {
        return $this->hasMany('App\Models\Answer');
    }
}
