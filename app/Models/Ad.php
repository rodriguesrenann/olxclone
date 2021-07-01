<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';

    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
