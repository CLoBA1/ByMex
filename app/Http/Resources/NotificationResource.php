<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'icon' => $this->type === 'reservation_new' ? 'fa-solid fa-ticket' : 'fa-solid fa-bell',
            'color' => $this->type === 'reservation_new' ? '#1e40af' : '#64748b',
            'title' => $this->title,
            'desc' => $this->message,
            'link' => $this->link,
            'whatsapp_link' => $this->whatsapp_link,
            'time' => $this->created_at->diffForHumans()
        ];
    }
}
