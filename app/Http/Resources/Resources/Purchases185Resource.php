<?php


namespace App\Http\Resources\Resources;


use App\Http\Resources\ApiResource;
use App\MongoObject\MongoObject;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class Purchases185Resource extends ApiResource
{

    function __construct($resource)
    {
        parent::__construct($resource);
        $this->data = new MongoObject($this);
    }

    public function toArray($request)
    {

        $data = [
            "purchase" => $this->data->get("commonInfo.purchaseNumber"),
            "fz" => 615,
            "contract_concluded" => null,
            "object" => $this->data->get("commonInfo.purchaseObjectInfo"),
            "date_publish" => $this->data->getUnix("commonInfo.docPublishDate"),
            "contract_region" => $this->data->get("notificationInfo.contractCondition.kladrPlacesInfo.kladrPlace.kladr.fullName") .
                $this->data->get("notificationInfo.contractCondition.kladrPlacesInfo.kladrPlace.deliveryPlace"),
            "provider_ident_type" => $this->data->get("placingWayInfo.name"),
            "placer_full_name" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.fullName"),
            "placer_spz" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.regNum"),
            "placer_sr" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.consRegistryNum"),
            "placer_role" => $this->data->get("purchaseResponsibleInfo.publisherRole"),
            "start_price" => $this->data->get("notificationInfo.contractCondition.maxPriceInfo.maxPrice"),
            "execSum" => $this->data->get("notificationInfo.contractCondition.maxPriceInfo.contractGuarantee.amount"),
            "partSum" => $this->data->get("notificationInfo.contractCondition.maxPriceInfo.applicationGuarantee.amount"),
        ];

        $data['customers'] = [
            "customer_code" => null,
            "customer_full_name" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.fullName"),
            "customer_inn" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.INN"),
            "customer_kpp" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.KPP"),
            "customer_ogrn" => null,
            "customer_address" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.postAddress"),
            "postal_address" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.postAddress"),
            "customer_oktmo" => null,
            "customer_rf_subject" => null,
            "customer_role" => $this->data->get("purchaseResponsibleInfo.publisherRole"),
            "customer_spz" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.regNum"),
            "customer_sr" => $this->data->get("purchaseResponsibleInfo.responsibleOrgInfo.consRegistryNum"),
        ];

        return $data;
    }

}
