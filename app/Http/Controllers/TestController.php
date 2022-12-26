<?php

namespace App\Http\Controllers;

use App\Http\Resources\Resources\Purchases185Resource;
use App\Http\Resources\Resources\Purchases223Resource;
use App\Http\Resources\Resources\Purchases44Resource;
use App\Http\Resources\TestResources;
use App\MongoObject\MongoObject;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    protected $connectionName = "eis";

    function compareJson(){
        $eis = collect(json_decode(file_get_contents("eis_new.json"),true));
        $eis = $eis->keyBy("purchaseNumber")->toArray();

        $test = collect(json_decode(file_get_contents("test_new.json"),true));
        $test = $test->keyBy("purchaseNumber")->toArray();

        $compare = [];
        foreach ($eis as $k => $value){
            $compare[$k] = $this->arrayEach($value,new MongoObject($test[$k]));
        }
        //file_put_contents("compare.json",json_encode($compare,256));
        $keys = [];
        foreach ($compare as $compar){
            foreach ($compar as $value){
                isset($keys[$value]) ? $keys[$value]++ : $keys[$value] = 1;
            }
        }
        ///$keys = collect($keys)->unique();
        dd($keys);
    }

    function arrayEach($arr,MongoObject $arr2,$prefix = []){
        $compare = [];
        foreach ($arr as $k => $value){
            if($k == "_id") continue;
            $prefix_ = $prefix;
            $prefix_[] = $k;
            if(is_array($value)){
                $compare_ = $this->arrayEach($value,$arr2,$prefix_);
                $compare = array_merge($compare,$compare_);
            }else{
                $key = implode(".",$prefix_);
                $value_2 = $arr2->get($key);
                if(is_null($value_2)){
                    $compare[] = $key;
                }elseif($value != $value_2){
                    $compare[] = $key;
                }elseif($value == $value_2){
                    //$compare[] = $key." - true";
                }
            }
        }
        return $compare;
    }

    function testAction(){

        $num = request()->get("num");

        $response = null;
        $customers = null;
        if(strlen($num) == 11){
            echo "<h1>purchases_223</h1>";
            $response = $this->DB->table("purchases_223")
                    ->where("registrationNumber",$num)->get()
                    ->first() ?? [];
        }elseif(strlen($num) == 19){
            echo "<h1>purchases_44</h1>";
            $response = $this->DB->table("purchases_44")
                    ->where("purchaseNumber",$num)
                    ->get()
                    ->first() ?? [];
            $customers = $this->DB->table("organizations_44")
                    ->where("regNumber",$response['lot']['customerRequirements']['customerRequirement']['customer']['regNum'])
                    ->get() ?? [];
        }elseif(strlen($num) == 18){
            echo "<h1>purchases_185</h1>";
            $response = $this->DB->table("purchases_185")
                    ->where("commonInfo.purchaseNumber",$num)->get()
                    ->first() ?? [];
        }

        echo "<pre>";
        print_r($response);
        print_r($customers);
//        print_r($protocols_44);
        exit;


        $purchases = $this->DB->table("purchases_44")
            ->limit(3000)
            ->offset(7777)
            ->get()
            ->filter(function ($item,$i){
                return $i % 100 === 0;
            })
            ->map(function ($item){
                return $item['purchaseNumber'];
            })->values();

        echo $purchases->toJson();
        exit;



        foreach ($purchases as $purchase){
            $this->getStructure($purchase,$this->structure);
        }
        dd($this->structure);
    }

    private $structure = [];

    function getStructure($arr,&$str){
        foreach ($arr as $key => $value){
            if(!key_exists($key,$str)){
                $str[$key] = [];
                if(is_array($value)){
                    $this->getStructure($value,$str[$key]);
                }
                if(count($str[$key]) == 0){
                    $str[$key] = null;
                }
            }
        }
    }
}
