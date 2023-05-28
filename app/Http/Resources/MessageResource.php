<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'message'   => $this->message,
            'user'      => new UserResource($this->whenLoaded('user')),
            'chat'      => new ChatResource($this->whenLoaded('chat'))
        ];
    }
}
