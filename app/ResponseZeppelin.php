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
            return false;
        }
        $body = (array)$response['body'];

        if(!isset($body['msg'])){
            return false;
        }
        $data = $body['msg'];

        return $data;
    }
}
