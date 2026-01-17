<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'in_stock' => 'nullable|in:true,false,1,0',
            'rating_from' => 'nullable|numeric|min:0|max:5',
            'sort' => [
                'nullable',
                'string',
                Rule::in(['price_asc', 'price_desc', 'rating_desc', 'newest']),
            ],
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'sort.in' => 'Неправильно значение сортировки. Доступны: price_asc, price_desc, rating_desc, newest.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        return array_merge(parent::validated(), [
            'page' => $this->input('page', 1),
            'limit' => $this->input('limit', 20),
        ]);
    }
}
