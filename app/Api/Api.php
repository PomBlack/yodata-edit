<?php


namespace App\Api;


use Illuminate\Support\Facades\DB;

class Api
{
    protected $connectionName = null;

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
}
