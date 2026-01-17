<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class ProductResource extends JsonResource
{
    #[OA\Schema(
        schema: 'Product',
        properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'name', type: 'string', example: 'iPhone 15 Pro'),
            new OA\Property(property: 'price', type: 'float', example: 999.99),
            new OA\Property(property: 'category_id', type: 'integer', example: 1),
            new OA\Property(property: 'category', type: 'string', example: 'Электроника'),
            new OA\Property(property: 'in_stock', type: 'boolean', example: true),
            new OA\Property(property: 'rating', type: 'float', example: 4.8),
            new OA\Property(property: 'created_at', type: 'string', example: '2026-01-17T21:06:06.000000Z'),
            new OA\Property(property: 'updated_at', type: 'string', example: '2026-01-17T21:06:06.000000Z'),
        ],
        type: 'object'
    )]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (float)$this->price,
            'category_id' => $this->category_id,
            'category' => $this->category->name ?? null,
            'in_stock' => (bool)$this->in_stock,
            'rating' => (float)$this->rating,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }
}
