<?php

namespace App\Events;

use App\Models\ProductQuestion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ProductQuestion $question)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('product.' . $this->question->product_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'QuestionCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->question->id,
            'product_id' => $this->question->product_id,
            'customer_name' => $this->question->customer_name,
            'question' => $this->question->question,
            'admin_answer' => $this->question->admin_answer,
            'answered_at' => $this->question->answered_at?->toISOString(),
            'created_at' => $this->question->created_at?->toISOString() ?? now()->toISOString(),
        ];
    }
}
