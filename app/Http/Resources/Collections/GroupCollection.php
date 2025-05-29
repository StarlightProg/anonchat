<?php

namespace App\Http\Api\Resources;

use App\Http\Resources\GroupResource;
use App\Models\DriverRequest;

class GroupCollection extends PaginateCollection
{
    public $collects = GroupResource::class;

    public function toArray($request): array
    {
        return array_merge(
            [
                'groups' => $this->collection,
            ],
            parent::toArray($request)
        );
    }
}
