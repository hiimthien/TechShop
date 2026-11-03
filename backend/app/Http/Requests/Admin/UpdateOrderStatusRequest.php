<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái đơn hàng là bắt buộc.',
            'status.in'       => 'Trạng thái đơn hàng không hợp lệ.',
        ];
    }
}

