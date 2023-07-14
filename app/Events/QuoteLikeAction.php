<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteLikeAction implements ShouldBroadcast, ShouldQueue
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $like;

	/**
	 * Create a new event instance.
	 */
	public function __construct($like)
	{
		$this->like = [$like];
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return array<int, \Illuminate\Broadcasting\Channel>
	 */
	public function broadcastOn(): array
	{
		return [
			new Channel('quote-like-action'),
		];
	}
}
