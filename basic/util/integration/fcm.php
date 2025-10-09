<?php

namespace Allen\Basic\Util\Integration;

use Exception;
use Google\Client;
use Allen\Basic\Path;
use Allen\Basic\Util\{Config, Json, Request};

class FCM
{
	const PRIORITY_MIN = 'PRIORITY_MIN';
	const PRIORITY_LOW = 'PRIORITY_LOW';
	const PRIORITY_DEFAULT = 'PRIORITY_DEFAULT';
	const PRIORITY_HIGH = 'PRIORITY_HIGH';
	const PRIORITY_MAX = 'PRIORITY_MAX';
	private ?array $data = null;
	public function SetData(string $key, string $value): self
	{
		if ($this->data === null) {
			$this->data = [];
		}
		$this->data[$key] = $value;
		return $this;
	}
	private ?string $notification_priority = null;
	public function SetNotificationPriority(string $priority): self
	{
		$this->notification_priority = $priority;
		return $this;
	}
	private ?bool $direct_boot_ok = null;
	public function SetDirectBootOk(bool $ok): self
	{
		$this->direct_boot_ok = $ok;
		return $this;
	}
	public function _SetOpenURL(string $url): self
	{
		$this->SetData('action', 'open_url')->SetData('url', $url);
		return $this;
	}
	public function Output(): array
	{
		return [
			'data' => $this->data,
			'notification_priority' => $this->notification_priority,
			'direct_boot_ok' => $this->direct_boot_ok,
		];
	}
	static private string $project_id;
	static private string $access_token;
	static public function AccessToken(): string
	{
		if (isset(self::$access_token)) {
			return self::$access_token;
		}
		Config::Init();
		$file = Path::Setting(Config::Get('util.fcm', 'integration/fcm.json'));
		if (!file_exists($file)) {
			throw new Exception('FCM setting file not found: ' . $file);
		}
		$data = file_get_contents($file);
		if ($data === false || !json_validate($data)) {
			throw new Exception('FCM setting file invalid: ' . $file);
		}
		$data = Json::Decode($data);
		if (!isset($data['project_id']) || !is_string($data['project_id']) || empty($data['project_id'])) {
			throw new Exception('FCM setting file invalid: ' . $file);
		}
		self::$project_id = $data['project_id'];
		$client = new Client();
		$client->useApplicationDefaultCredentials();
		$client->setAuthConfig($data);
		$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
		$token = $client->fetchAccessTokenWithAssertion();
		var_dump($token);
		$access_token = $token['access_token'] ?? null;
		if (!$access_token) {
			throw new Exception('FCM access token fetch failed');
		}
		self::$access_token = $access_token;
		return self::$access_token;
	}
	static public function Send(string $title, string $message, string|array $receivers, ?FCM $option = null): null|false|array
	{
		if (is_string($receivers)) {
			$receivers = [$receivers];
		}
		$receivers = array_unique($receivers);
		if (count($receivers) === 0) {
			return null;
		}
		$send = [
			'message' => [
				'notification' => [
					'title' => $title,
					'body' => $message,
				],
			],
		];
		if ($option) {
			$options = $option->Output();
			if (!is_null($options['data'])) {
				$send['message']['data'] = $options['data'];
			}
			if (!is_null($options['notification_priority'])) {
				$send['message']['android']['notification']['notification_priority'] = $options['notification_priority'];
			}
			if (!is_null($options['direct_boot_ok'])) {
				$send['message']['android']['direct_boot_ok'] = $options['direct_boot_ok'];
			}
		}
		$success = [];
		$access_token = self::AccessToken();
		$project_id = self::$project_id ?? throw new Exception('FCM project ID not set');
		$request = new Request(
			url: 'https://fcm.googleapis.com/v1/projects/' . $project_id . '/messages:send',
			header: [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			],
		);
		foreach ($receivers as $receiver_name => $receiver) {
			$send['message']['token'] = $receiver;
			$result = $request->POST(Json::Encode($send));
			if ($result['code'] >= 200 && $result['code'] < 300) $success[$receiver_name] = $result;
		}
		return count($success) === 0 ? false : $success;
	}
}
