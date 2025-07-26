<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\User\UserBaseResource;
use Illuminate\Http\Request;

class UserLoginResource extends UserBaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = parent::toArray($request);

        return array_merge($parent, [
            'token' => $this->createToken($this->name)->plainTextToken,
        ]);
    }
}
