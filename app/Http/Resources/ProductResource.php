<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'featured_image'    => $this->featured_image,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description,
            'price'             => $this->price,
            'status'            => $this->status,
            'posted_by'         => new UserResource($this->whenLoaded('postedBy')),
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'galleries'         => GalleryResource::collection($this->whenLoaded('product_galleries')),
            'ratings'           => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}