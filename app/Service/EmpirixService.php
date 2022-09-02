<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class EmpirixService {

    protected $host = "klerity.internalwom.com";
    protected $username;
    protected $password;

    function __construct($username,$password) {
        $this->username = $username;
        $this->password = $password;
    }

    static public function makeUrl($lat,$long){
        return  "https://www.google.com/maps/@$lat,$long";
    }

    function getToken()
    {
        $result = Http::withoutVerifying()->withHeaders([
            'X-OpenAM-Username' => $this->username,
            'X-OpenAM-Password' => $this->password
        ])->post($this->baseurl() . '/openam/json/authenticate', []);
        return $result->json()["tokenId"];
    }

    function getData($page= 1,$limit = 10,$ac,$cell)
    {
        $result = Http::withoutVerifying()->withCookies(["iPlanetDirectoryPro"=>$this->getToken()],$this->host)->withHeaders([
            'Accepted-version' => '1.1.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->get($this->baseurl() . '/ers-mdm/api/enrichments/dim_cells',
        [
            'limit' => $limit,
            'filter' =>"ac=".$ac."[AND]"."cell=".$cell
        ]);

    return $result->json();
    }

    function getCoordenadas($ac,$cell,$page= 1,$limit = 10){
        $lat=22;
        $long=23;

        $result = $this->getData($page,$limit,$ac,$cell);

        if(count($result["data"]) > 0){
            $data = $result["data"];
            $last = $data[count($data)-1];
            dd($last);
            return self::makeUrl($last[$lat],$last[$long]);
        }
        return null;
    }

    function baseUrl(){
        return "https://".$this->host;
    }
}
