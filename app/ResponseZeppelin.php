<?php

namespace App;

class ResponseZeppelin {

    static function validResponse(array $response){

        if(!isset($response['body'])){
            return false;
        }
        $body = (array)$response['body'];
        if(!isset($body["code"])){
            return false;
        }
        return $body["code"] == "SUCCESS";
    }

    static function getDataResponse(array $response){

        if(!isset($response['body'])){
            return null;
        }
        $body = (array)$response['body'];

        if(!isset($body['msg'])){
            return null;
        }
        $data = $body['msg'];

        if(is_array($data)){
            $joined = "";
            for ($i=0; $i < count($data); $i++) {
                $arrayData = (array)$data[$i];
                $joined .= $arrayData['data'];
            }

            return $joined;
        }

        return $data;
    }
}
