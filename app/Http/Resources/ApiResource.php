<?php


namespace App\Http\Resources;


use App\MongoObject\MongoObject;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource extends JsonResource
{
    /**
     * @var MongoObject
     */
    protected $data;
    function __construct($resource)
    {
        parent::__construct($resource);
        //$this->data = new MongoObject($this);
    }
}
