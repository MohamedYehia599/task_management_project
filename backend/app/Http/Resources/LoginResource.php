<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'name' => $this['user']->name,
                'email' => $this['user']->email,
                'role' => $this['user']->role,

            ],
            'tokens' => [
                'accessToken' => $this['tokens']['accessToken'],
                'refreshToken' =>  $this['tokens']['refreshToken'], 
            ],
        ];
    }
}
