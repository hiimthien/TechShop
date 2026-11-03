<?php

namespace App\Events;

use App\Models\ProductReview;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ProductReview $review)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('product.' . $this->review->product_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReviewCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'customer_name' => $this->review->customer_name,
            'customer_phone' => $this->review->customer_phone,
            'rating' => (int) $this->review->rating,
            'content' => $this->review->content,
            'admin_reply' => $this->review->admin_reply,
            'replied_at' => $this->review->replied_at?->toISOString(),
            'is_verified_purchase' => (bool) $this->review->is_verified_purchase,
            'created_at' => $this->review->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
