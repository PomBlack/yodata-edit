<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use \Illuminate\Database\ConnectionInterface;

class Controller extends BaseController
{
    protected $connectionName = null;
    /**
     * @var ConnectionInterface
     */
    protected $DB = null;

    function __construct()
    {
        try {
            $this->getConnection();
        } catch (\Exception $e) {
            abort(500,__CLASS__.":".$e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    function getConnection(){
        if(is_null($this->connectionName)){
            throw new \Exception('$connectionName cannot be null !!');
        }
        $this->DB = DB::connection($this->connectionName);
    }

    function response($data){
        return response()->json(
            $data,
            200,
            [
                'Content-Type' => 'application/json;charset=UTF-8',
                'Charset' => 'utf-8',
            ],
            JSON_UNESCAPED_UNICODE
        );
    }
}
