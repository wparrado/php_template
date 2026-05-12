<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers\Api\V1;

use Application\Commands\Category\CreateCategoryCommand;
use Application\Commands\Category\DeleteCategoryCommand;
use Application\Commands\Category\UpdateCategoryCommand;
use Application\DTOs\CategoryOutputDTO;
use Application\DTOs\PaginationDTO;
use Application\Ports\CategoryApplicationServiceInterface;
use Application\Queries\Category\GetCategoryQuery;
use Application\Queries\Category\ListCategoriesQuery;
use Application\Result\Failure;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Presentation\Http\Requests\Category\CreateCategoryRequest;
use Presentation\Http\Requests\Category\UpdateCategoryRequest;
use Presentation\Http\Resources\CategoryResource;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryApplicationServiceInterface $service,
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->service->list(new ListCategoriesQuery(
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
                fn ($dto) => (new CategoryResource($dto))->toArray(request()),
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
        $result = $this->service->getById(new GetCategoryQuery(categoryId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var CategoryOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new CategoryResource($dto))->toArray(request())]);
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->service->create(new CreateCategoryCommand(
            name: (string) $validated['name'],
            description: isset($validated['description']) ? (string) $validated['description'] : null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var CategoryOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new CategoryResource($dto))->toArray(request())], 201);
    }

    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->service->update(new UpdateCategoryCommand(
            categoryId: $id,
            name: (string) $validated['name'],
            description: isset($validated['description']) ? (string) $validated['description'] : null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var CategoryOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new CategoryResource($dto))->toArray(request())]);
    }

    public function destroy(string $id): JsonResponse
    {
        $result = $this->service->delete(new DeleteCategoryCommand(categoryId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        return new JsonResponse(null, 204);
    }
}
