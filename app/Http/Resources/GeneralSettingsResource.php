<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'zip' => $this->zip,
            'city' => $this->city,
            'email' => $this->email,
            'isProxyCardFeatureAvailable' => $this->is_proxy_card_feature_available,
        ];
    }
}
