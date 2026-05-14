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
use OpenApi\Attributes as OA;

final class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryApplicationServiceInterface $service,
    ) {}

    #[OA\Get(
        path: '/api/v1/categories',
        summary: 'List categories',
        tags: ['Categories'],
        responses: [new OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(type: 'array', items: new OA\Items(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )))]
    )]
    public function index(): JsonResponse
    {
        $result = $this->service->list(new ListCategoriesQuery(
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

    #[OA\Get(
        path: '/api/v1/categories/{id}',
        summary: 'Get category',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Successful response', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
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

    #[OA\Post(
        path: '/api/v1/categories',
        summary: 'Create category',
        tags: ['Categories'],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
    public function store(CreateCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var array{name: string, description?: string|null} $validated */
        $result = $this->service->create(new CreateCategoryCommand(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var CategoryOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new CategoryResource($dto))->toArray(request())], 201);
    }

    #[OA\Put(
        path: '/api/v1/categories/{id}',
        summary: 'Update category',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        )),
        responses: [new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
            ]
        ))]
    )]
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();

        /** @var array{name: string, description?: string|null} $validated */
        $result = $this->service->update(new UpdateCategoryCommand(
            categoryId: $id,
            name: $validated['name'],
            description: $validated['description'] ?? null,
        ));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        /** @var CategoryOutputDTO $dto */
        $dto = $result->getValue();

        return new JsonResponse(['data' => (new CategoryResource($dto))->toArray(request())]);
    }

    #[OA\Delete(
        path: '/api/v1/categories/{id}',
        summary: 'Delete category',
        tags: ['Categories'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 204, description: 'No Content')]
    )]
    public function destroy(string $id): JsonResponse
    {
        $result = $this->service->delete(new DeleteCategoryCommand(categoryId: $id));

        if ($result instanceof Failure) {
            throw $result->getError();
        }

        return new JsonResponse(null, 204);
    }
}
