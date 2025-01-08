<?php

namespace App\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'               => $this->id ?? null,
            'name'             => $this->name ?? null,
            'guard_name'       => $this->guard_name ?? null,
            'permission_ids'   => $this->permissions->pluck('id') ?? null,
            'permission_names' => $this->permissions->pluck('name') ?? null,
        ];
    }
}