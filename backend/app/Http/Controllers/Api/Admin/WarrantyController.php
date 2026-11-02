<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = WarrantyClaim::with(['user:id,name,email', 'order:id,ghn_order_code,created_at']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('claim_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('product_name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $warranties = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'data' => $warranties,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $claim = WarrantyClaim::with(['user', 'order'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $claim,
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,received,inspecting,completed,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $claim = WarrantyClaim::findOrFail($id);
        $claim->status = $request->status;

        if ($request->has('admin_note')) {
            $claim->admin_note = $request->admin_note;
        }

        if (in_array($request->status, ['completed', 'rejected'])) {
            $claim->resolved_at = now();
        }

        $claim->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật trạng thái phiếu bảo hành thành công',
            'data' => $claim,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $claim = WarrantyClaim::findOrFail($id);
        $claim->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xóa phiếu bảo hành',
        ]);
    }
}

