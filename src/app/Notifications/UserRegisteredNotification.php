<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification {
	use Queueable;

	public function via(object $notifiable): array {
		return ['mail'];
	}

	public function toMail(object $notifiable): MailMessage {
		return (new MailMessage)
			->subject('【AIナレッジシステム 】ユーザー登録のお知らせ')
			->line('お疲れ様です。')
			->line('AIナレッジシステムへユーザ登録が完了しました。')
			->line('以上です。')
			->line('よろしくお願いいたします。');
	}
}
