<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers\Api\V1;

use Application\Commands\Item\CreateItemCommand;
use Application\Commands\Item\DeleteItemCommand;
use Application\Commands\Item\UpdateItemCommand;
use Application\DTOs\ItemOutputDTO;
use Application\DTOs\PaginationDTO;
use Application\Ports\ItemApplicationServiceInterface;
use Application\Queries\Item\GetItemQuery;
use Application\Queries\Item\ListItemsQuery;
use Application\Result\Failure;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Presentation\Http\Requests\Item\CreateItemRequest;
use Presentation\Http\Requests\Item\UpdateItemRequest;
use Presentation\Http\Resources\ItemResource;
use OpenApi\Attributes as OA;

final class ItemController extends Controller
{
    public function __construct(
        private readonly ItemApplicationServiceInterface $service,
    ) {}

    #[OA\Get(
        path: '/api/v1/items',
        summary: 'List items',
        tags: ['Items'],
        responses: [new OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )))]
    )]
    public function index(): JsonResponse
    {
        $result = $this->service->list(new ListItemsQuery(
            limit: (int) (request()->query('limit') ?? 20),
            offset: (int) (request()->query('offset') ?? 0),
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var PaginationDTO $pagination */
        $pagination = $result->getValue();

        return new JsonResponse([
            'data' => array_map(
                fn ($dto) => (new ItemResource($dto))->toArray(request()),
                $pagination->items
            ),
            'meta' => [
                'total' => $pagination->total,
                'limit' => $pagination->limit,
                'offset' => $pagination->offset,
                'has_more' => $pagination->hasMore(),
            ],
        ]);
    }

    #[OA\Get(
        path: '/api/v1/items/{id}',
        summary: 'Get item',
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
    public function show(string $id): JsonResponse
    {
        $result = $this->service->getById(new GetItemQuery(itemId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var ItemOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new ItemResource($dto))->toArray(request())]);
    }

    #[OA\Post(
        path: '/api/v1/items',
        summary: 'Create item',
        tags: ['Items'],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
    public function store(CreateItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var array{name: string, price: float|int, currency?: string, description?: string|null, category_id?: string|null} $validated */
        $result = $this->service->create(new CreateItemCommand(
            name: $validated['name'],
            price: (float) $validated['price'],
            currency: $validated['currency'] ?? 'USD',
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var ItemOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new ItemResource($dto))->toArray(request())], 201);
    }

    #[OA\Put(
        path: '/api/v1/items/{id}',
        summary: 'Update item',
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )),
        responses: [new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int64'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'price', type: 'number', format: 'float'),
                new OA\Property(property: 'currency', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'categoryId', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
    public function update(UpdateItemRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        /** @var array{name: string, price: float|int, currency?: string, description?: string|null, category_id?: string|null} $validated */
        $result = $this->service->update(new UpdateItemCommand(
            itemId: $id,
            name: $validated['name'],
            price: (float) $validated['price'],
            currency: $validated['currency'] ?? 'USD',
            description: $validated['description'] ?? null,
            categoryId: $validated['category_id'] ?? null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var ItemOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new ItemResource($dto))->toArray(request())]);
    }

    #[OA\Delete(
        path: '/api/v1/items/{id}',
        summary: 'Delete item',
        tags: ['Items'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 204, description: 'No Content')]
    )]
    public function destroy(string $id): JsonResponse
    {
        $result = $this->service->delete(new DeleteItemCommand(itemId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        return new JsonResponse(null, 204);
    }
}
