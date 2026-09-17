<?php

namespace Modules\Content\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'imageUrl' => $this->image_url,
            'altText' => $this->alt_text,
            'sortOrder' => $this->sort_order,
        ];
    }
}
