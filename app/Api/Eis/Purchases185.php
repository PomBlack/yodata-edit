<?php


namespace App\Api\Eis;


use App\Http\Resources\Resources\Purchases185Resource;
use App\Http\Resources\Resources\Purchases44Resource;

class Purchases185 extends Eis implements EisInterface
{

    function find($num)
    {
        $purchase = $this->DB->table("purchases_185")
                    ->where("commonInfo.purchaseNumber",$num)->get()
                    ->first();
        if(!$purchase){
            abort(404,"purchase not found");
        }
        $response = Purchases185Resource::make(collect($purchase));
        return $response;
    }
}
