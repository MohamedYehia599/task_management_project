<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskShowResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date->format('Y-m-d'),
            

            'assigned_to' => [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
                'email' => $this->assignedUser->email,
            ],
            

            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ],

            'dependencies' => $this->when(
                $this->relationLoaded('dependencies'),
                function () {
                    return $this->dependencies->map(function ($dependency) {
                        return [
                            'id' => $dependency->id,
                            'title' => $dependency->title,
                            'description'=>$dependency->description,
                            'status' => $dependency->status,
                            'due_date' => $dependency->due_date->format('Y-m-d'),
                        ];
                    });
                }
            ),
            
            
            'can_be_completed' => $this->canBeCompleted(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}