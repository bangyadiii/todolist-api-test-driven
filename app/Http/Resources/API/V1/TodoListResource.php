<?php

declare(strict_types=1);

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "type" => "todolist",
            "attributes" => [
                "title" => $this->title,
                "description" => $this->description,
                "user_id" => $this->user_id,
            ],
            "relationship" => [
                "user" => $this->whenLoaded("user", function () {
                    return new UserResource($this->user);
                })
            ],
            "links" => [
                "self" => \route("api.todolist.show", ["todolist" => $this->id]),
                "parent" => \route("api.todolist.index"),
            ]

        ];
    }
}
