<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                Rule::exists('addresses', 'id')->where('user_id', $this->user()?->id),
            ],
            'shipping_fee'   => 'required|numeric|min:0',
            'coupon_code'    => 'nullable|string|max:50',
            'payment_method' => 'nullable|string|in:cod,vnpay,bank_transfer',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required'   => 'Vui lòng chọn địa chỉ nhận hàng.',
            'address_id.exists'     => 'Địa chỉ nhận hàng không hợp lệ hoặc không thuộc tài khoản của bạn.',
            'shipping_fee.required' => 'Thiếu thông tin phí vận chuyển.',
            'shipping_fee.numeric'  => 'Phí vận chuyển phải là một số hợp lệ.',
            'payment_method.in'     => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
