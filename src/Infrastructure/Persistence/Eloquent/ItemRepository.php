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
        return ItemModel::query()
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
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
            id: (string) $model->getAttribute('id'),
            name: (string) $model->getAttribute('name'),
            price: (float) $model->getAttribute('price'),
            currency: (string) $model->getAttribute('currency'),
            description: $model->getAttribute('description') !== null ? (string) $model->getAttribute('description') : null,
            categoryId: $model->getAttribute('category_id') !== null ? (string) $model->getAttribute('category_id') : null,
            createdAt: new \DateTimeImmutable((string) $model->getAttribute('created_at')),
            updatedAt: new \DateTimeImmutable((string) $model->getAttribute('updated_at')),
            deleted: (bool) $model->getAttribute('is_deleted'),
        );
    }
}
