<?php


namespace App\Api\Eis;


class EisFactory
{
    static function make($num):EisInterface{
        $class = "";
        switch (strlen($num)){
            case 11:
                $class = Purchases233::class;
                break;
            case 19:
                $class = Purchases44::class;
                break;
            case 18:
                $class = Purchases185::class;
                break;
        }
        return new $class($num);
    }
}
