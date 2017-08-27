<?php

namespace App\Console\Commands;

use Gitter\Client;
use Illuminate\Console\Command;
use Telegram;

class UpdateGitterStatus extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gitter:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update Gitter Message Status';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$client = new Client(env('GITTER_TOKEN'));

		$client->connect();
		$room = $client->rooms->findByName(env('GITTER_ROOM_NAME'));
		$unreadMessages = $client->users->unreadItems($room['id']);

		if (empty($unreadMessages['chat'])) {
			return;
		}
		foreach ($unreadMessages['chat'] as $message) {
			$msg = $client->messages->find($room['id'], $message);
			$text = '@' . $msg['fromUser']['username'] . ' ' . $msg['text'];

			Telegram::sendMessage(
				[
					'chat_id' => env('TELEGRAM_BOT_GROUP_ID'),
					'text'    => $text
				]
			);

			\Log::info(
				'send',
				[
					'chat_id' => env('TELEGRAM_BOT_GROUP_ID'),
					'text'    => $text
				]
			);
		}

		$client->users->markAsRead($room['id'], $unreadMessages['chat']);
	}
}