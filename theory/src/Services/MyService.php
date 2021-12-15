<?php

namespace App\Services;

class MyService
{
    use OptionalServiceTrait;

    public function __construct($param, $globalParam, MySecondService $serviceParam)
    {
        dump($param);
        dump($globalParam);
    }


}
