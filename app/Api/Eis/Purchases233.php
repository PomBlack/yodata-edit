<?php


namespace App\Api\Eis;


use App\Http\Resources\Resources\Purchases223Resource;
use App\Http\Resources\Resources\Purchases44Resource;
use App\MongoObject\MongoObject;

class Purchases233 extends Eis implements EisInterface
{

    function find($num)
    {
        $purchase = $this->DB->table("purchases_223")
            ->where("registrationNumber",$num)
            ->first();
        if(!$purchase){
            abort(404,"purchase not found");
        }
        $response = Purchases223Resource::make($purchase);
        return $response;
    }
}
