<?php

declare(strict_types=1);

namespace Application\Mappers;

use Application\DTOs\ItemOutputDTO;
use Domain\Model\Example\Item;

final class ItemMapper
{
    public function toDTO(Item $item): ItemOutputDTO
    {
        return new ItemOutputDTO(
            id: $item->getId()->toString(),
            name: $item->getName()->getValue(),
            price: $item->getPrice()->getAmount(),
            currency: $item->getPrice()->getCurrency(),
            description: $item->getDescription()->getValue(),
            categoryId: $item->getCategoryId()?->toString(),
            createdAt: $item->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $item->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Item[] $items
     * @return ItemOutputDTO[]
     */
    public function toDTOList(array $items): array
    {
        return array_map($this->toDTO(...), $items);
    }
}
