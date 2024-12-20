<?php

namespace App\Http\Resources\V1;

use App\Models\Tickets;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'users',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'isManager' => $this->is_manager,
                $this->mergeWhen(
                    $request->routeIs('authors.*'), [
                        'emailVerifiedAt' => $this->email_verified_at,
                        'createdAt' => $this->created_at,
                        'updatedAt' => $this->updated_at,
                    ]
                ),
            ],
            'includes' => TicketsResource::collection($this->whenLoaded('tickets')),
            'links' => [
                'self' => route('authors.show',['author' => $this->id])
            ]
        ];
    }
}
