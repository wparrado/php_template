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
use Application\Result\Success;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Presentation\Http\Requests\Item\CreateItemRequest;
use Presentation\Http\Requests\Item\UpdateItemRequest;
use Presentation\Http\Resources\ItemResource;

final class ItemController extends Controller
{
    public function __construct(
        private readonly ItemApplicationServiceInterface $service,
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->service->list(new ListItemsQuery(
            limit: (int) request()->query('limit', 20),
            offset: (int) request()->query('offset', 0),
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var PaginationDTO $pagination */
        $pagination = $result->getValue();

        return new JsonResponse([
            'data' => array_map(
                fn (ItemOutputDTO $dto) => (new ItemResource($dto))->toArray(request()),
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

    public function store(CreateItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

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

    public function update(UpdateItemRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

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

    public function destroy(string $id): JsonResponse
    {
        $result = $this->service->delete(new DeleteItemCommand(itemId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        return new JsonResponse(null, 204);
    }
}
