<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Model\Example\Category;
use Domain\Ports\Outbound\CategoryRepositoryInterface;
use Infrastructure\Persistence\Eloquent\Models\CategoryModel;

final class CategoryRepository implements CategoryRepositoryInterface
{
    public function save(Category $category): void
    {
        CategoryModel::query()->updateOrCreate(
            ['id' => $category->getId()->toString()],
            [
                'name' => $category->getName()->getValue(),
                'description' => $category->getDescription()->getValue(),
            ]
        );
    }

    public function findById(string $id): ?Category
    {
        /** @var CategoryModel|null $model */
        $model = CategoryModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    /**
     * @return Category[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        return CategoryModel::query()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(fn (CategoryModel $model) => $this->toDomain($model))
            ->all();
    }

    public function delete(string $id): void
    {
        CategoryModel::query()->where('id', $id)->delete();
    }

    public function count(): int
    {
        return (int) CategoryModel::query()->count();
    }

    private function toDomain(CategoryModel $model): Category
    {
        return Category::reconstitute(
            id: (string) $model->getAttribute('id'),
            name: (string) $model->getAttribute('name'),
            description: $model->getAttribute('description') !== null ? (string) $model->getAttribute('description') : null,
            createdAt: new \DateTimeImmutable((string) $model->getAttribute('created_at')),
            updatedAt: new \DateTimeImmutable((string) $model->getAttribute('updated_at')),
        );
    }
}
