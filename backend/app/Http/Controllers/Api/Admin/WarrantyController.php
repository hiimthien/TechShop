<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarrantyClaim;
use App\Services\WarrantyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function __construct(
        protected WarrantyService $warrantyService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $warranties = $this->warrantyService->getAdminPaginated($filters, $perPage);

        return $this->successResponse($warranties);
    }

    public function show(string $id): JsonResponse
    {
        $claim = WarrantyClaim::with(['user:id,name,email', 'order:id,created_at'])->findOrFail($id);

        return $this->successResponse($claim);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status'     => 'required|in:pending,received,inspecting,processing,completed,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $claim = $this->warrantyService->updateStatus(
            (int) $id,
            (string) $request->status,
            $request->input('admin_note')
        );

        return $this->successResponse(
            $claim,
            'Cập nhật trạng thái phiếu bảo hành thành công.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->warrantyService->deleteClaim((int) $id);

        return $this->successResponse(
            null,
            'Đã xóa phiếu bảo hành thành công.'
        );
    }
}
