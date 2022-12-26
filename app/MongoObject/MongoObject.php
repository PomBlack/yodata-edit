<?php


namespace App\MongoObject;


use Carbon\Carbon;

class MongoObject
{
    private $array = [];

    function __construct($array)
    {
        $this->array = $array;
    }

    function get($keys){
        return $this->_get($keys,$this->array);
    }

    private function _get($keys,$arr){
        $keys_arr = explode(".",$keys);
        $key = array_shift($keys_arr);
        if(isset($arr[$key])){
            return count($keys_arr) == 0 ? $arr[$key] : $this->_get(implode(".",$keys_arr),$arr[$key]);
        }
        return null;
    }

    public function getObject($keys){
        return new MongoObject($this->_get($keys,$this->array));
    }

    function getDate($keys,$format = "d-m-Y"){
        $value = $this->get($keys);
        return $value ? Carbon::make($value)->format($format) : null;
    }

    function getUnix($keys,$format = "d-m-Y"){
        $value = $this->get($keys);
        return $value ? Carbon::make($value->toDateTime())->format($format) : null;
    }
}
