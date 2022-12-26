<?php


namespace App\Http\Controllers;

use App\Api\Eis\EisFactory;
use App\Http\Resources\Resources\Purchases185Resource;
use App\Http\Resources\Resources\Purchases223Resource;
use App\Http\Resources\Resources\Purchases44Resource;
use App\MongoObject\MongoObject;
use Illuminate\Http\Request;

class EisController extends Controller
{
    protected $connectionName = "eis";

    function find(Request $request){

        $purchase = $request->get("purchase");
        $resource = EisFactory::make($purchase);
        $response = $resource->find($purchase);

        return $this->response($response);
    }
}
