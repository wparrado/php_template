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
        /** @var \Illuminate\Database\Eloquent\Collection<int, CategoryModel> $models */
        $models = CategoryModel::query()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models
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
            id: $model->id,
            name: $model->name,
            description: $model->description,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString()),
        );
    }
}
