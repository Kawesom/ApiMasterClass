<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketsResource extends JsonResource
{
    //public static $wrap = 'bitches';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'tickets',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'description' => $this->when(
                    $request->routeIs('tickets.show'),
                    $this->description,
                ),
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at
            ],
            'relationships' => [
                'author' => [
                    'data' =>  [
                        'type' => 'user',
                        'id' => $this->id
                    ],
                    'links' => [
                        ['self' => 'TODO']
                    ]
                ]
            ],
            'includes' => [
                new UsersResource($this->users),
            ],
            'links' => [
                ['self' => route('tickets.show',['ticket' => $this->id])]
            ]
        ];
    }
}
