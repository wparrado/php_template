<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property float $price
 * @property string $currency
 * @property string|null $description
 * @property string|null $category_id
 * @property bool $is_deleted
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class ItemModel extends Model
{
    protected $table = 'items';

    protected $keyType = 'string';

    public $incrementing = false;

    /** @var list<string> */
    protected $fillable = [
        'id',
        'name',
        'price',
        'currency',
        'description',
        'category_id',
        'is_deleted',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'price' => 'float',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
