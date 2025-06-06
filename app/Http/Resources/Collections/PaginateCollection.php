<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginateCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'page' => (int) $this->currentPage(),
            'limit' => (int) $this->perPage(),
            'total' => (int) $this->total(),
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setContent(json_encode(collect(json_decode($response->getContent()))->shift()));
    }
}
