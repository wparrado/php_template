<?php

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Item',
    type: 'object',
    required: ['id','name'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', format: 'int64'),
        new OA\Property(property: 'name', type: 'string'),
    ]
)]
class ItemSchema {}

#[OA\Schema(
    schema: 'Category',
    type: 'object',
    required: ['id','name'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
    ]
)]
class CategorySchema {}
