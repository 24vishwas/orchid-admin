<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryResource extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Get lang param from request
        $lang = $request->route('lang') ?? 'en';

        $translation = $this->translations->where('locale', $lang)->first();
        return [
            'id'       => $this->id,
            'title'    => $translation?->title ?? null,
            'image_url'=> $this->image_path 
                          ? url($this->image_path)  // 👈 handles full path
                          : null,
            'active'   => $this->active,
        ];
    }
}
