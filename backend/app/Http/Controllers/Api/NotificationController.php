<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của người dùng hiện tại
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at');

        // Lọc theo danh mục nếu có
        $category = $request->query('category');
        if ($category && in_array($category, ['order', 'warranty', 'coupon', 'system'])) {
            $query->where('data', 'like', '%"category":"' . $category . '"%');
        }

        $perPage = (int) $request->query('per_page', 20);
        $notifications = $query->paginate($perPage);

        // Đếm số lượng thông báo chưa đọc
        $unreadCount = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Format data JSON cho frontend
        $items = collect($notifications->items())->map(function ($item) {
            $data = json_decode($item->data, true) ?: [];
            return [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $data['title'] ?? 'Thông báo',
                'message' => $data['message'] ?? '',
                'category' => $data['category'] ?? 'system',
                'link' => $data['link'] ?? '/account',
                'icon' => $data['icon'] ?? 'notifications',
                'reference_id' => $data['reference_id'] ?? null,
                'is_read' => !is_null($item->read_at),
                'read_at' => $item->read_at,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $items,
            'unread_count' => $unreadCount,
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead(string $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $affected = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        $unreadCount = DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Đã đánh dấu thông báo đã đọc',
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        DB::table('notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Đã đánh dấu tất cả thông báo là đã đọc',
            'unread_count' => 0,
        ]);
    }
}

