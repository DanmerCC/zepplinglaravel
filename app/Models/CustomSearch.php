<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'hour',
        'day',
        'output',
        'pid'
    ];

    protected $casts = ['day' => 'date'];
}
