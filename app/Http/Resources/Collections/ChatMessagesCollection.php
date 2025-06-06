<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\Collections\PaginateCollection;
use App\Models\DriverRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatMessagesCollection extends PaginateCollection
{
    public $collects = ChatMessageResource::class;

    public function toArray($request): array
    {
        return array_merge(
            [
                'messages' => $this->collection
            ],
            parent::toArray($request)
        );
    }
}
