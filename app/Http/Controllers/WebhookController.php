<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram;

class WebhookController extends Controller
{
	public function telegram()
	{
		$update = Telegram::getWebhookUpdates();
		$message = $update->getMessage();

		$chat_name = $message->getChat()->getTitle();
		$user_name = $message->getFrom()->getUsername();

		\Log::info(
			'message',
			[
				'chat_name' => $chat_name,
				'username'  => $user_name,
				'text'      => $message->getText()
			]
		);
	}
}
