<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\WarrantyClaim;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WarrantyService
{
    /**
     * Create a new warranty claim and notify relevant parties.
     */
    public function createClaim(array $data, ?User $user = null): WarrantyClaim
    {
        $claimCode = 'BH-' . strtoupper(Str::random(6));

        $claim = WarrantyClaim::create([
            ...$data,
            'claim_code' => $claimCode,
            'user_id'    => $user?->id,
            'status'     => 'pending',
        ]);

        $this->notifyClaimCreated($claim, $user);

        return $claim;
    }

    /**
     * Search claims and purchased devices by serial/IMEI, claim code or phone number.
     */
    public function lookup(string $search): array
    {
        $search = trim($search);

        $claims = WarrantyClaim::where('claim_code', $search)
            ->orWhere('customer_phone', $search)
            ->orWhere('serial_number', $search)
            ->latest()
            ->get();

        // Tra cứu thông tin thiết bị và hạn bảo hành theo Serial / IMEI
        $orderItems = \App\Models\OrderItem::with(['order.user', 'variant.product'])
            ->where('serial_number', $search)
            ->get()
            ->map(function ($item) {
                $purchaseDate = $item->order?->created_at;
                $warrantyMonths = 12; // Bảo hành tiêu chuẩn 12 tháng
                $expiresAt = $purchaseDate ? $purchaseDate->copy()->addMonths($warrantyMonths) : null;
                $isExpired = $expiresAt ? now()->greaterThan($expiresAt) : false;

                return [
                    'serial_number'    => $item->serial_number,
                    'product_name'     => $item->variant?->product?->name ?? 'Thiết bị TechShop',
                    'order_code'       => '#TS-' . str_pad((string) $item->order_id, 6, '0', STR_PAD_LEFT),
                    'purchase_date'    => $purchaseDate?->format('d/m/Y'),
                    'warranty_expires' => $expiresAt?->format('d/m/Y'),
                    'is_expired'       => $isExpired,
                    'status_label'     => $isExpired ? 'Hết hạn bảo hành' : 'Còn hạn bảo hành chính hãng',
                    'warranty_months'  => $warrantyMonths,
                ];
            });

        return [
            'claims'  => $claims,
            'devices' => $orderItems,
        ];
    }

    /**
     * Get claims for a specific customer.
     */
    public function getUserClaims(int $userId): Collection
    {
        return WarrantyClaim::where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Get paginated claims for admin view.
     */
    public function getAdminPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WarrantyClaim::query()->latest();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $keyword = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('claim_code', 'like', $keyword)
                    ->orWhere('customer_name', 'like', $keyword)
                    ->orWhere('customer_phone', 'like', $keyword)
                    ->orWhere('product_name', 'like', $keyword)
                    ->orWhere('serial_number', 'like', $keyword);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Update claim status and note, with customer notification.
     */
    public function updateStatus(int $id, string $status, ?string $adminNote = null): WarrantyClaim
    {
        $claim = WarrantyClaim::findOrFail($id);

        $claim->status = $status;
        if ($adminNote !== null) {
            $claim->admin_note = $adminNote;
        }
        $claim->save();

        $this->notifyStatusUpdated($claim);

        return $claim;
    }

    /**
     * Delete a warranty claim.
     */
    public function deleteClaim(int $id): bool
    {
        $claim = WarrantyClaim::findOrFail($id);
        return (bool) $claim->delete();
    }

    /**
     * Send notifications when a claim is created.
     */
    protected function notifyClaimCreated(WarrantyClaim $claim, ?User $user = null): void
    {
        try {
            // 1. Notify Admins
            NotificationService::sendToAdmins('warranty_created', [
                'title'        => 'Yêu cầu bảo hành mới #' . $claim->claim_code,
                'message'      => 'Khách hàng ' . ($claim->customer_name ?: 'Khách') . ' vừa gửi yêu cầu bảo hành cho ' . $claim->product_name . '.',
                'category'     => 'warranty',
                'link'         => '/admin/warranties',
                'icon'         => 'build',
                'reference_id' => $claim->id,
            ]);

            // 2. Notify User if authenticated
            if ($user) {
                NotificationService::sendToUser($user, 'warranty_created', [
                    'title'        => 'Tiếp nhận bảo hành #' . $claim->claim_code,
                    'message'      => 'Yêu cầu bảo hành cho ' . $claim->product_name . ' đã được ghi nhận. Kỹ thuật viên sẽ liên hệ sớm.',
                    'category'     => 'warranty',
                    'link'         => '/account',
                    'icon'         => 'verified',
                    'reference_id' => $claim->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi thông báo bảo hành: ' . $e->getMessage());
        }
    }

    /**
     * Send notifications when claim status updates.
     */
    protected function notifyStatusUpdated(WarrantyClaim $claim): void
    {
        try {
            $statusLabels = [
                'pending'    => 'Đang tiếp nhận',
                'processing' => 'Đang kiểm tra & sửa chữa',
                'completed'  => 'Đã hoàn tất bảo hành',
                'rejected'   => 'Từ chối bảo hành',
            ];
            $label = $statusLabels[$claim->status] ?? $claim->status;

            if ($claim->user) {
                NotificationService::sendToUser($claim->user, 'warranty_status_updated', [
                    'title'        => 'Phiếu bảo hành #' . $claim->claim_code . ' cập nhật',
                    'message'      => 'Phiếu bảo hành của bạn đã chuyển sang trạng thái: ' . $label . '.',
                    'category'     => 'warranty',
                    'link'         => '/account',
                    'icon'         => $claim->status === 'completed' ? 'check_circle' : 'build',
                    'reference_id' => $claim->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi gửi thông báo cập nhật bảo hành: ' . $e->getMessage());
        }
    }
}

