<?php

namespace App\Interfaces;

interface IProcessor
{

    public function getName():string;
    public function getPathOutput():string;
    public function getParamsRules():array;
}
