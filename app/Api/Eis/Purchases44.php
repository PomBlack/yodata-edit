<?php


namespace App\Api\Eis;


use App\Http\Resources\Resources\Purchases44Resource;
use App\MongoObject\MongoObject;

class Purchases44 extends Eis implements EisInterface
{

    function find($num)
    {
        $purchase = $this->DB->table("purchases_44")
                ->where("purchaseNumber",$num)
                ->first();
        if(!$purchase){
            abort(404,"purchase not found");
        }
        $purchase_obj = new MongoObject($purchase);
        $customers = $purchase_obj->get('lot.customerRequirements.customerRequirement');
        $customers_list = [];
        $q = 0;
        if(is_array($customers)){
            foreach ($customers as $k => $cust){
                if(!is_numeric($k)) continue;
                $q++;
                $customer = new MongoObject($cust);
                $reg_num = $customer->get("customer.regNum");
                $customers_list[$reg_num] = $this->DB->table("organizations_44")
                    ->where("regNumber",$reg_num)
                    ->first();
            }
        }
        if($q == 0){
            $reg_num = $purchase_obj->get('lot.customerRequirements.customerRequirement.customer.regNum');
            $customers_list[$reg_num] = $this->DB->table("organizations_44")
                ->where("regNumber",$reg_num)
                ->first();
        }
        $purchase['customer_list'] = $customers_list;
        $response = Purchases44Resource::make(collect($purchase));
        return $response;
    }
}
