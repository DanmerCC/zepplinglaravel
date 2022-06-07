<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'paragraph_id',
        'notebook_id',
        'outout',
        'response',
        'process_id'
    ];


}
