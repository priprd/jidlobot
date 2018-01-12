<?php

namespace App\Model;

use GuzzleHttp\Client;
use Nette;

/**
 * Slack model
 */
class SlackModel
{
	use Nette\SmartObject;

	/**
	 * @param string $text
	 * @param string $url
	 * @return void
	 */
	public function postResponse($text, $url)
	{
		$data = [
			'text' =>  $text,
			'mrkdwn' => TRUE,
		];

		$client = new Client([
			'timeout'  => 20,
		]);

		$client->post($url, [
			'json' => $data
		]);
	}
}