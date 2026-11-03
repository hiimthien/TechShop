<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BroadcastPromotionNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public string $type,
        public array $payload
    ) {}

    /**
     * Broadcast notification to all customers in chunks to prevent memory bloat or request timeout.
     */
    public function handle(): void
    {
        Log::info("[BroadcastPromotionNotificationJob] Starting broadcast for promotion...");

        User::where('role', 'customer')
            ->orWhereNull('role')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    try {
                        NotificationService::sendToUser($user, $this->type, $this->payload);
                    } catch (\Throwable $e) {
                        Log::warning("[BroadcastPromotionNotificationJob] Failed notifying user #{$user->id}: " . $e->getMessage());
                    }
                }
            });

        Log::info("[BroadcastPromotionNotificationJob] Finished broadcast.");
    }
}

