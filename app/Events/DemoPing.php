<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class DemoPing implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(public array $payload) {}

    // public channel so Postman needs no auth
    public function broadcastOn(): Channel
    {
        return new Channel('restaurant.189.branch.209'); // -> "public.demo"
    }

    // Nice short event name on the wire
    public function broadcastAs(): string
    {
        return 'order.placed';
    }
}
