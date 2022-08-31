<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_publica',
        'hour',
        'day',
        'output',
        'pid',
        'state'
    ];

    protected $casts = ['day' => 'date'];

    function updateState()
    {
    }
}
