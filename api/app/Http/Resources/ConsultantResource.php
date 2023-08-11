<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */

class ConsultantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'co_usuario' => $this->co_usuario,
            'no_usuario' => $this->no_usuario,
        ];
    }
}
