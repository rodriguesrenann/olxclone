<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';

    public $timestamps = false;
    
    protected $fillable = ['user_id', 'state', 'title', 'images', 'price', 'price_negotiable', 'status', 'views', 'created_at'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
