<?php


namespace App\Http\Resources\Resources;


use App\Http\Resources\ApiResource;
use App\MongoObject\MongoObject;
use Illuminate\Support\Str;

class Purchases223Resource extends ApiResource
{
    function __construct($resource)
    {
        parent::__construct($resource);
        $this->data = new MongoObject($this);
    }

    public function toArray($request)
    {
        $data = [
            "purchase" => $this->data->get("registrationNumber"),
            "fz" => 223,
            "contract_concluded" => $this->data->get("contact") ? true : false,
            "provider_ident_type" => $this->data->get("purchaseCodeName"),
            "date_publish" => $this->data->getUnix("publicationDateTime")
        ];
        $lots = $this->data->get("lots.lot");

        $lots_ = [];
        foreach ($lots as $k => $lot) {
            if (!is_numeric($k)) continue;
            $lots_[] = $lot;
        }
        if (empty($lots_)) {
            $lots_[] = $lots;
        }
        $data['objects'] = [];
        $objects_ = [];
        foreach ($lots_ as $lot) {
            $lot = (new MongoObject($lot));
            $objects = $lot->get("lotData.lotItems.lotItem");
            foreach ($objects as $k => $object) {
                if (!is_numeric($k)) continue;
                $object['name'] = $lot->get("lotData.subject");
                $objects_[] = $object;
            }
            if (empty($data['objects'])) {
                $objects['name'] = $lot->get("lotData.subject");
                $objects_[] = $objects;
            }
        }

        foreach ($objects_ as $k => $object_) {
            $object = (new MongoObject($object_));
            $data['objects'][] = [
                "purchase_lot" => ($k + 1),
                "name" => $object->get('name'),
                "okdp" => $object->get('okdp.code'),
                "okpd2" => $object->get('okpd2.code'),
                "okved" => $object->get('okved.code'),
                "okved2" => $object->get('okved2.code'),
                "price" => $object->get("commodityItemPrice"),
                "sum" => "", /// ???
                "quantity" => "", /// ???
            ];
        }

        $customer = $this->data->getObject("customer.mainInfo");
        $kpp = $customer->get("kpp");
        $data['customers'] = [
            "customer_code" => $customer->get("iko"),
            "customer_full_name" => $customer->get("fullName"),
            "customer_inn" => $customer->get("inn"),
            "customer_kpp" => $kpp,
            "customer_ogrn" => $customer->get("ogrn"),
            "customer_address" => $customer->get("legalAddress"),
            "postal_adress" => $customer->get("postalAddress"),
            "customer_oktmo" => null, /// ???
            "customer_rf_subject" => Str::substr($kpp,0,2),
            "start_price" => $this->data->get("lots.lot.lotData.initialSum"),
            "contract_region" => $this->data->get("lots.lot.lotData.deliveryPlace.state")
        ];

        return $data;
    }
}
