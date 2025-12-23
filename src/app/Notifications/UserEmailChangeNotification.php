<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChangeNotification extends Notification {
	use Queueable;

	public function __construct(
		private string $token,
		private string $newEmail,
		private string $name
	) {}

	public function via($notifiable): array {
		return ['mail'];
	}

	public function toMail($notifiable): MailMessage {
		$url = route('users.email-verify-change', ['token' => $this->token]);

		return (new MailMessage)
			->subject('【AIナレッジシステム】新しいメールアドレス変更のお知らせ')
			->greeting("{$this->name} 様")
			->line('お疲れ様です。')
			->line('AIナレッジシステムの新しいメールアドレスを変更しました。')
			->action('トークンリンク', $url)
			->line('以上です。')
			->line('よろしくお願いいたします。');
	}
}
