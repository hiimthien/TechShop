<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderSerialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'                 => 'required|array|min:1',
            'items.*.id'            => 'required|exists:order_items,id',
            'items.*.serial_number' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'      => 'Danh sách sản phẩm là bắt buộc.',
            'items.*.id.required' => 'Mã định danh sản phẩm đơn hàng là bắt buộc.',
            'items.*.id.exists'   => 'Sản phẩm trong đơn hàng không tồn tại.',
        ];
    }
}

