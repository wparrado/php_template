<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Model\Example\Item;
use Domain\Ports\Outbound\ItemRepositoryInterface;
use Infrastructure\Persistence\Eloquent\Models\ItemModel;

final class ItemRepository implements ItemRepositoryInterface
{
    public function save(Item $item): void
    {
        ItemModel::query()->updateOrCreate(
            ['id' => $item->getId()->toString()],
            [
                'name' => $item->getName()->getValue(),
                'price' => $item->getPrice()->getAmount(),
                'currency' => $item->getPrice()->getCurrency(),
                'description' => $item->getDescription()->getValue(),
                'category_id' => $item->getCategoryId()?->toString(),
                'is_deleted' => $item->isDeleted(),
            ]
        );
    }

    public function findById(string $id): ?Item
    {
        /** @var ItemModel|null $model */
        $model = ItemModel::query()->find($id);

        if ($model === null) {
            return null;
        }

        return $this->toDomain($model);
    }

    /**
     * @return Item[]
     */
    public function findAll(int $limit = 20, int $offset = 0): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, ItemModel> $models */
        $models = ItemModel::query()
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models
            ->map(fn (ItemModel $model) => $this->toDomain($model))
            ->all();
    }

    public function delete(string $id): void
    {
        ItemModel::query()->where('id', $id)->delete();
    }

    public function count(): int
    {
        return (int) ItemModel::query()->where('is_deleted', false)->count();
    }

    private function toDomain(ItemModel $model): Item
    {
        return Item::reconstitute(
            id: $model->id,
            name: $model->name,
            price: $model->price,
            currency: $model->currency,
            description: $model->description,
            categoryId: $model->category_id,
            createdAt: new \DateTimeImmutable($model->created_at->toDateTimeString()),
            updatedAt: new \DateTimeImmutable($model->updated_at->toDateTimeString()),
            deleted: $model->is_deleted,
        );
    }
}
