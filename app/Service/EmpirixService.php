<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class EmpirixService {

    protected $baseurl = "https://klerity.internalwom.com";
    protected $username;
    protected $password;

    function __construct($username,$password) {
        $this->username = $username;
        $this->password = $password;
    }

    static public function getCoordenadas($params){
        return  "https://www.google.com/maps/@-12.1801335,-76.9782796,15z";
    }

    function getToken()
    {
        $result = Http::withoutVerifying()->withHeaders([
            'X-OpenAM-Username' => $this->username,
            'X-OpenAM-Password' => $this->password
        ])->post($this->baseurl . '/openam/json/authenticate', []);
        return $result->json()["tokenId"];
    }
}
