<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Process extends Model
{
    use HasFactory;

    protected  $fillable = [
        'status',
        'out_descompress'
    ];

    protected $appends =['out_casted'];

    protected $with = ['results'];

    function getOutCastedAttribute(){

        if($this->out_descompress==null){
            return null;
        }
        $array_result =  (((array)json_decode($this->out_descompress)));
        $noprocesable = "no procesable";
        if(isset($array_result["body"])){

            if(is_array($array_result["body"]->msg)){
                return array_map(function($item){
                    Log::info($item->data);
                    $item->data = utf8_encode($item->data);
                    return $item->data;
                },$array_result["body"]->msg);
            }else{

                return $noprocesable;
            }
        }

        return (((array)json_decode($this->out_descompress)));
    }

    /**
     * Get all of the results for the Process
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Result::class, 'process_id', 'id');
    }
}
