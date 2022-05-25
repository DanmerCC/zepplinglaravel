<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharagraphResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'paragraph_id',
        'book_id',
        'status',
        'result',

    ];
}
