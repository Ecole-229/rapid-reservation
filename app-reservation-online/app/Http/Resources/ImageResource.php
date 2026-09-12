<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $url = null;
        if (!empty($this->path)) {
            if (filter_var($this->path, FILTER_VALIDATE_URL)) {
                $url = $this->path;
            } else {
                $url = asset(Storage::url($this->path));
            }
        }

        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'path' => $this->path,
            'url' => $url,
            'designation' => $this->designation,
            'salle_id' => (int) $this->salle_id,
            'salle' => new SalleResource($this->whenLoaded('salle')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
