<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;

final class CreateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['nullable', 'string', 'uuid'],
        ];
    }
}
