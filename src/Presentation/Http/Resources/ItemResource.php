<?php

declare(strict_types=1);

namespace Presentation\Http\Resources;

use Application\DTOs\ItemOutputDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ItemOutputDTO $resource
 */
final class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'price' => [
                'amount' => $this->resource->price,
                'currency' => $this->resource->currency,
            ],
            'description' => $this->resource->description,
            'category_id' => $this->resource->categoryId,
            'created_at' => $this->resource->createdAt,
            'updated_at' => $this->resource->updatedAt,
        ];
    }
}
