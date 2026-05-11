<?php

declare(strict_types=1);

namespace Application\Mappers;

use Application\DTOs\CategoryOutputDTO;
use Domain\Model\Example\Category;

final class CategoryMapper
{
    public function toDTO(Category $category): CategoryOutputDTO
    {
        return new CategoryOutputDTO(
            id: $category->getId()->toString(),
            name: $category->getName()->getValue(),
            description: $category->getDescription()->getValue(),
            createdAt: $category->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $category->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Category[] $categories
     * @return CategoryOutputDTO[]
     */
    public function toDTOList(array $categories): array
    {
        return array_map($this->toDTO(...), $categories);
    }
}
