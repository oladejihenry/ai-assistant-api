<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TravelResponseUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $message,
        public string $response
    ) {
    }


    public function broadcastOn()
    {
        return new Channel('travel');
    }

    public function broadcastWith(): array
    {

        return [
            'message' => $this->message,
            'response' => $this->response,
        ];
    }
}
