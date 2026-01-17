<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Products',
    description: 'API Endpoints for Product management'
)]
class ProductController extends Controller
{

    #[OA\Get(
        path: '/api/products',
        summary: 'Получение продуктов с учетом фильтра',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                description: 'Поиск по имени',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'price_from',
                description: 'Цена от',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'number')
            ),
            new OA\Parameter(
                name: 'price_to',
                description: 'Цена до',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'number')
            ),
            new OA\Parameter(
                name: 'category_id',
                description: 'Id категории',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'in_stock',
                description: 'В наличии',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'rating_from',
                description: 'Рейтинг от',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'number')
            ),
            new OA\Parameter(
                name: 'sort',
                description: 'Сортировка',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean', enum: ['price_asc', 'price_desc', 'rating_desc', 'newest'])
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Текущая страница',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Кол-во товаров на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 20)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductCollection')
            )
        ]
    )]
    public function index(ProductIndexRequest $request): ProductCollection
    {
        $query = Product::with('category');

        if ($request->filled('q')) {
            $query->where('name', 'LIKE', '%' . $request->input('q') . '%');
        }

        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->input('price_from'));
        }

        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->input('price_to'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('in_stock')) {
            $inStock = filter_var($request->in_stock, FILTER_VALIDATE_BOOLEAN);
            $query->where('in_stock', $inStock);
        }

        if ($request->filled('rating_from')) {
            $query->where('rating', '>=', $request->input('rating_from'));
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(
            perPage: $request->input('limit'),
            page: $request->input('page')
        );

        return new ProductCollection($products);
    }
}
