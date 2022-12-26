<?php


namespace App\Http\Resources\Resources;


use App\Http\Resources\ApiResource;
use App\MongoObject\MongoObject;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class Purchases44Resource extends ApiResource
{

    function __construct($resource)
    {
        parent::__construct($resource);
        $this->data = new MongoObject($this);
    }

    public function toArray($request)
    {



        $data = [
            "purchase" => $this->data->get("purchaseNumber"),
            "purchaseCode" => $this->data->get("lot.customerRequirements.customerRequirement.purchaseCode"),
            "contract_concluded" => $this->data->get("contractConclusionOnSt83Ch2") ? true : false,
            "fz" => "44",
            "contract_regNum" => null, //// ????
            "auction_date" => $this->data->getUnix("createDate") ?? $this->data->getUnix("procedureInfo.bidding.date"),
            "object" => $this->data->get("purchaseObjectInfo"),
            "start_price" => (string)$this->data->get("lot.maxPrice"),
            "final_price" => null,
            "execSum" => null,
            "partSum" => (string)$this->data->get("lot.customerRequirements.customerRequirement.applicationGuarantee.amount"),
        ];

        $data['lots'] = [

        ];

        $data['objects'] = [];
        $objects = $this->data->get("lot.purchaseObjects.purchaseObject");
        $sum = 0;
        if (is_array($objects)) {
            foreach ($objects as $k => $object) {
                if (!is_numeric($k)) continue;
                $object = new MongoObject($object);
                $data['objects'][] = [
                    "purchase_lot" => ($k + 1),
                    "name" => $object->get('name'),
                    "okpd" => $object->get('okpd.code'),
                    "okpd2" => $object->get('OKPD2.code'),
                    "ktru" => $object->get('KTRU.code'),
                    "price" =>(string)$object->get('price'),
                    "sum" =>(string)$object->get('sum'),
                    "quantity" =>(string)$object->get('quantity.value'),
                ];
                $sum += $object->get("sum");
            }
        }

        if (empty($data['objects'])) {
            $object = $this->data->getObject("lot.purchaseObjects.purchaseObject");
            $data['objects'][] = [
                "purchase_lot" => 1,
                "name" => $object->get("name"),
                "okpd2" => $object->get("OKPD2.code"),
                "okpd" => $object->get("okpd.code"),
                "ktru" => $object->get("KTRU.code"),
                "price" => (string)$object->get("price"),
                "sum" => (string)$object->get("sum"),
                "quantity" => (string)$object->get("quantity.value")
            ];
            $sum += $object->get("sum");
        }

        $data['okpd2'] = [$object->get("OKPD2.code")];

        $data +=[
            "date_publish" => $this->data->getUnix("docPublishDate"),
            "date_end" => $this->data->getUnix("procedureInfo.collecting.endDate"),
            "contract_region" => $this->data->get("lot.customerRequirements.customerRequirement.kladrPlaces.kladrPlace.deliveryPlace"),
            "provider_ident_type" => $this->data->get("placingWayInfo.name") ?? $this->data->get("placingWay.name"),
            "contract_region" => $this->data->get("lot.customerRequirements.customerRequirement.kladrPlaces.kladrPlace.deliveryPlace"),
        ];


        $data['customers'] = [];
        $customers = $this->data->get('lot.customerRequirements.customerRequirement');
        $q = 0;
        if (is_array($customers)) {
            foreach ($customers as $k => $cust) {
                if (!is_numeric($k)) continue;
                $q++;
                $cust['customer_list'] = $this->data->get("customer_list");
                $customer = new MongoObject($cust);
                $data['customers'][] = $this->makeCustomer($customer);
            }
        }
        if ($q == 0) {
            $customers['customer_list'] = $this->data->get("customer_list");
            $data['customers'][] = $this->makeCustomer(new MongoObject($customers));
        }
        $data +=[
            "placer_full_name" => $this->data->get("protocolPublisher.publisherOrg.fullName") ?? $this->data->get("purchaseResponsible.responsibleOrg.fullName"),
            "placer_role" => $this->data->get("purchaseResponsible.responsibleRole"),
            "placer_spz" => $this->data->get("purchaseResponsible.responsibleOrg.regNum"),
            "placer_sr" => $this->data->get("purchaseResponsible.responsibleOrg.consRegistryNum"),
            "placer_code" => $this->data->get("lot.customerRequirements.customerRequirement.IKZInfo.customerCode")
        ];
        return $data;
    }

    function makeCustomer(MongoObject $customer)
    {
        $regnum = $customer->get("customer.regNum");
        $customer_info = new MongoObject($customer->get("customer_list")[$regnum]);
        $str = $customer->get('IKZInfo.customerCode');
        $kpp = $customer_info->get("KPP");
        $inn = $customer_info->get("INN");
        $execSum = floatval($customer->get("contractGuarantee.amount"));
        $amount = floatval($customer->get("contractGuarantee.amount"));
        $part = floatval($customer->get("contractGuarantee.part"));
        $partSum = $part > 0 ? $amount / $part : null;
        return [
            "contract_regNum" => $regnum,
            "customer_code" => $customer->get('IKZInfo.customerCode'),
            "customer_full_name" => $customer->get('customer.fullName'),
            "customer_inn" => $inn,
            "customer_kpp" => $kpp,
            "customer_ogrn" => $customer_info->get("OGRN"),
            "customer_address" => $customer->get("kladrPlaces.kladrPlace.deliveryPlace"),
            "postal_address" => $customer->get("kladrPlaces.kladrPlace.deliveryPlace"),
            "customer_oktmo" => $customer_info->get("OKTMO.code"),
            "customer_rf_subject" => Str::substr($kpp,0,2),
            "customer_role" => $this->data->get("purchaseResponsible.responsibleRole"),
            "customer_spz" => $customer->get("customer.regNum"),
            "customer_sr" => $customer->get("customer.consRegistryNum"),
            "start_price" => (string)$customer->get("maxPrice"),
            "final_price" => null,
            "execSum" => (string)$execSum,
            "partSum" => (string)$partSum,
        ];
    }


}
