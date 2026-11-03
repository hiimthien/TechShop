<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'variant_id.required' => 'Vui lòng chọn phiên bản sản phẩm.',
            'variant_id.exists'   => 'Phiên bản sản phẩm không hợp lệ.',
            'quantity.required'   => 'Số lượng mua là bắt buộc.',
            'quantity.min'        => 'Số lượng mua tối thiểu là 1.',
        ];
    }
}

