<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 */

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'co_cliente' => $this->co_cliente,
            'no_razao' => $this->no_razao,
            'no_fantasia' => $this->no_fantasia,
        ];
    }
}
