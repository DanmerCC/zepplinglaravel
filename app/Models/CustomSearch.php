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
        'state',
        'email'
    ];

    protected $casts = ['day' => 'date'];

    function updateState()
    {
    }

    /**
     * Get the user associated with the CustomSearch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
