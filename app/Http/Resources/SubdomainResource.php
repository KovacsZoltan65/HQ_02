<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubdomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'subdomain' => $this->subdomain, 
            'url' => $this->url, 
            'name' => $this->name,
            'db_host' => $this->db_host, 
            'db_port' => $this->db_port, 
            'db_name' => $this->db_name, 
            'db_user' => $this->db_user, 
            'db_password' => $this->db_password,
            'notification' => $this->notification, 
            'state_id' => $this->state_id, 
            'is_mirror' => $this->is_mirror, 
            'sso' => $this->sso,
            'access_control_system' => $this->access_control_system, 
            'last_export' => $this->last_export
        ];
    }
}
