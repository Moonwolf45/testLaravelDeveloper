<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "ProductCollection",
    properties: [
        new OA\Property(
            property: "items",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Product")
        ),
        new OA\Property(property: "total", type: "integer", example: 100),
        new OA\Property(property: "page", type: "integer", example: 2),
        new OA\Property(property: "limit", type: "integer", example: 20),
        new OA\Property(property: "pages", type: "integer", example: 1)
    ],
    type: "object"
)]
class ProductCollection extends ResourceCollection
{
    private array $pagination;

    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'page' => $resource->currentPage(),
            'limit' => $resource->perPage(),
            'pages' => $resource->lastPage(),
        ];

        parent::__construct($resource->getCollection());
    }

    public function toArray(Request $request): array
    {
        return [
            'items' => ProductResource::collection($this->collection),
            ...$this->pagination
        ];
    }
}
