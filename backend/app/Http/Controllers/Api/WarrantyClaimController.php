<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarrantyClaimRequest;
use App\Services\WarrantyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarrantyClaimController extends Controller
{
    public function __construct(
        protected WarrantyService $warrantyService
    ) {}

    public function store(StoreWarrantyClaimRequest $request): JsonResponse
    {
        $claim = $this->warrantyService->createClaim(
            $request->validated(),
            $request->user()
        );

        return $this->successResponse(
            $claim,
            'Gửi yêu cầu bảo hành thành công! Mã tra cứu của bạn là: ' . $claim->claim_code,
            201
        );
    }

    public function lookup(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string']);

        $claims = $this->warrantyService->lookup((string) $request->query('query'));

        return $this->successResponse($claims);
    }

    public function myClaims(Request $request): JsonResponse
    {
        $claims = $this->warrantyService->getUserClaims((int) $request->user()->id);

        return $this->successResponse($claims);
    }
}
