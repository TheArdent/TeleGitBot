<?php

namespace App\Http\Controllers;

use Exception;
use Gitter\Client;
use Illuminate\Http\Request;
use Log;
use Telegram;

class WebhookController extends Controller
{
	public function telegram()
	{
		$update = Telegram::getWebhookUpdates();

		try {
			$message = $update->getMessage();
			if (is_null($message)) {
				return;
			}
			$chat_name = $message->getChat()->getTitle();
			$chat_id = $message->getChat()->getId();
			$user_name = $message->getFrom()->getUsername();

			Log::info(
				'message',
				[
					'chat_name' => $chat_name,
					'chat_id'   => $chat_id,
					'username'  => $user_name,
					'text'      => $message->getText()
				]
			);
			if (!strcmp($chat_name,env('TELEGRAM_BOT_GROUP'))) {
				$client = new Client(env('GITTER_TOKEN'));
				$client->connect();
				$room = $client->rooms->findByName(env('GITTER_ROOM_NAME'));
				$text = "@{$user_name} {$message->getText()}";
				$client->messages->create($room['id'], $text);
			}
		}
		catch (Exception $exception) {
			Log::error(
				'Exception',
				[
					'message' => $exception->getMessage(),
					'code'    => $exception->getCode(),
					'trace'   => $exception->getTraceAsString()
				]
			);
		}


	}
}
