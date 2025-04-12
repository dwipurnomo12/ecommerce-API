<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'invoice_code'       => $this->invoice_code,
            'transaction_date'   => $this->transaction_date,
            'transaction_status' => $this->transaction_status,
            'total_amount'       => $this->total_amount,
            'customer'           => new UserResource($this->whenLoaded('customer')),
            'discount'           => new DiscountResource($this->whenLoaded('discount')),
        ];
    }
}
