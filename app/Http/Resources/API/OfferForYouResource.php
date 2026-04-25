<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferForYouResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'short_description_1' => $this->short_description_1,
            'short_description_2' => $this->short_description_2,
            'background_color'    => $this->background_color,
            'type'                => $this->type,
            'status'              => $this->status,
            'offer_image'         => getSingleMedia($this, 'offer_image', null),
        ];
    }
}
