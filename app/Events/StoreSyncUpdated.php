<?php

namespace App\Events;

use App\Enums\SyncStatusEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreSyncUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public SyncStatusEnum $status, public string $store_name)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('monitoring'),
        ];
    }
    public function broadcastAs(): string
    {
        return 'sync.updated';
    }
    public function broadcastWith(): array
    {
        return [
            'status' => $this->status->value,
            'store_name' => $this->store_name,
        ];
    }
}
