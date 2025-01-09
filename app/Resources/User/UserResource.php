<?php

namespace App\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->_id ?? null,
            'name'  => $this->name ?? null,
            'email' => $this->email ?? null,
            'roles' => $this->roles ?? null,
        ];
    }
}
