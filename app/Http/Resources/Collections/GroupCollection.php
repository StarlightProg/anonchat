<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\GroupResource;
use App\Http\Resources\Collections\PaginateCollection;
use App\Models\DriverRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupCollection extends PaginateCollection
{
    public $collects = GroupResource::class;

    public function toArray($request): array
    {
        return array_merge(
            [
                'groups' => $this->collection
            ],
            parent::toArray($request)
        );
    }
}
