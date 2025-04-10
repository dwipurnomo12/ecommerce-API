<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'discount_code' => $this->discount_code,
            'discount_name' => $this->discount_name,
            'discount_amount' => $this->discount_amount,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'is_active'     => $this->is_active,
        ];
    }
}