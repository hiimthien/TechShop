<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Gửi thông báo đến 1 User cụ thể
     */
    public static function sendToUser(User $user, string $type, array $payload): string
    {
        $id = (string) Str::uuid();

        DB::table('notifications')->insert([
            'id' => $id,
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => $payload['title'] ?? 'Thông báo mới',
                'message' => $payload['message'] ?? '',
                'category' => $payload['category'] ?? 'system', // order, warranty, coupon, system
                'link' => $payload['link'] ?? '/account',
                'icon' => $payload['icon'] ?? 'notifications',
                'reference_id' => $payload['reference_id'] ?? null,
            ], JSON_UNESCAPED_UNICODE),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    /**
     * Gửi thông báo đến tất cả Quản trị viên (Admin)
     */
    public static function sendToAdmins(string $type, array $payload): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            self::sendToUser($admin, $type, $payload);
        }
    }

    /**
     * Gửi thông báo broadcast đến tất cả khách hàng (chạy bất đồng bộ qua Queue Job)
     */
    public static function broadcastToCustomers(string $type, array $payload): void
    {
        \App\Jobs\BroadcastPromotionNotificationJob::dispatch($type, $payload);
    }
}

