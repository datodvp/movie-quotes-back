<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
	use Queueable, SerializesModels;

	protected string $url;

	/**
	 * Create a new message instance.
	 */
	public function __construct(User $user)
	{
		// Generate verification URL
		$this->url = URL::temporarySignedRoute(
			'verification.verify',
			now()->addMinutes(60),
			[
				'id'   => $user->id,
				'hash' => sha1($user->email),
			]
		);
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			from: new Address('moviequotes@gmail.com', 'Movie Quotes'),
			subject: 'Verify Email',
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			markdown: 'emails.verification',
			with: [
				'url' => $this->url,
			]
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [];
	}
}
