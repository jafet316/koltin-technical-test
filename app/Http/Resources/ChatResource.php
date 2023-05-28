<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'post'  => new PostResource($this->whenLoaded('post')),
            'user'  => new UserResource($this->whenLoaded('user')),
            'messages'  => MessageResource::collection($this->whenLoaded('messages'))
        ];
    }
}
