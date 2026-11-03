<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name'     => 'required|string|max:255',
            'customer_phone'    => 'required|string|max:20',
            'customer_email'    => 'nullable|email|max:255',
            'product_name'      => 'required|string|max:255',
            'serial_number'     => 'nullable|string|max:100',
            'purchase_date'     => 'nullable|date',
            'issue_description' => 'required|string',
            'address'           => 'nullable|string|max:500',
            'order_id'          => 'nullable|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'     => 'Vui lòng cung cấp họ và tên khách hàng.',
            'customer_phone.required'    => 'Số điện thoại liên hệ là bắt buộc.',
            'product_name.required'      => 'Tên thiết bị cần bảo hành là bắt buộc.',
            'issue_description.required' => 'Vui lòng mô tả chi tiết lỗi hoặc sự cố của thiết bị.',
        ];
    }
}

