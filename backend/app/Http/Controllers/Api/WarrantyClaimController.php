<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WarrantyClaimController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'product_name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'issue_description' => 'required|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $claimCode = 'BH-' . strtoupper(Str::random(6));

        $claim = WarrantyClaim::create([
            ...$validated,
            'claim_code' => $claimCode,
            'user_id' => $request->user()?->id,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Gửi yêu cầu bảo hành thành công! Mã tra cứu của bạn là: ' . $claimCode,
            'data' => $claim,
        ], 201);
    }

    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $search = trim($request->query('query'));

        $claims = WarrantyClaim::where('claim_code', $search)
            ->orWhere('customer_phone', $search)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $claims,
        ]);
    }

    public function myClaims(Request $request): JsonResponse
    {
        $claims = WarrantyClaim::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $claims,
        ]);
    }
}

