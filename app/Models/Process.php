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

    function getOutCastedAttribute(){
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
}
