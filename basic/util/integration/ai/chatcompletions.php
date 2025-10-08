<?php

namespace Allen\Basic\Util\Integration\Ai;

use Allen\Basic\Util\Integration\Ai;
use Allen\Basic\Util\Integration\Ai\Data\{Base, Message, Message\Content, Message\Role, Response, ApiType};
use Allen\Basic\Util\SSE;
use CurlHandle;

class ChatCompletions implements Base
{
	/**
	 * @param Message[] $messages
	 */
	public function __construct(
		protected readonly Ai $ai,
		protected string $model,
		protected array $messages = [],
		protected bool $stream = false,
	) {
		$this->messages = array_filter($messages, fn($m) => $m instanceof Message);
	}
	public function ToArray(): array
	{
		return array_filter([
			'model' => $this->model,
			'messages' => array_map(fn($m) => $m->ToArray(), $this->messages),
		]);
	}
	public function GetModel(): string
	{
		return $this->model;
	}
	public function SetModel(string $model): self
	{
		$this->model = $model;
		return $this;
	}
	public function GetMessages(): array
	{
		return $this->messages;
	}
	public function SetMessages(array $messages): self
	{
		$this->messages = array_filter($messages, fn($m) => $m instanceof Message);
		return $this;
	}
	public function AddMessage(Message ...$messages): self
	{
		$this->messages = array_merge($this->messages, $messages);
		return $this;
	}
	public function IsStream(): bool
	{
		return $this->stream;
	}
	public function SetStream(bool $stream): self
	{
		$this->stream = $stream;
		return $this;
	}
	public function OpenAI(): array
	{
		$data = [
			'model' => $this->model,
			'messages' => array_values(array_map(fn($m) => $m->OpenAI(), $this->messages)),
		];
		if ($this->stream) {
			$data['stream'] = true;
		}
		return $data;
	}
	public function GoogleAI(): array
	{
		return [
			'contents' => array_values(array_map(fn($m) => $m->GoogleAI(), $this->messages)),
		];
	}
	/**
	 * 發送聊天請求
	 * @param Content[]|string|null $content
	 */
	public function Run(
		Role $role = Role::User,
		array|string|null $content = null,
	): ?Response {
		if (!empty($content)) {
			if (is_string($content)) {
				$content = [
					$content,
				];
			}
			$this->AddMessage(new Message($role, $content));
		}
		$header = [
			'Content-Type' => 'application/json',
		];
		$path = match ($this->ai->api_type) {
			ApiType::OpenAI => '/chat/completions',
			ApiType::GoogleAI => '/models/' . $this->model . ($this->stream ? ':streamGenerateContent?alt=sse' : ':generateContent'),
			default => null,
		};
		if (is_null($path)) {
			return null;
		}
		$send = match ($this->ai->api_type) {
			ApiType::OpenAI => $this->OpenAI(),
			ApiType::GoogleAI => $this->GoogleAI(),
			default => null,
		};
		if (is_null($send)) {
			return null;
		}
		$request = $this->ai->_RequestPOST(
			path: $path,
			header: $header,
			body: json_encode($send, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
			stream: $this->stream ? fn(CurlHandle $ch, string $data) => SSE::Send($data) : null,
		);
		$data = match ($this->ai->api_type) {
			ApiType::OpenAI => Response::FromOpenAI($request),
			ApiType::GoogleAI => Response::FromGoogleAI($request),
			default => null,
		};
		if ($data instanceof Response) {
			if (!is_null($data->GetMessage())) {
				$this->AddMessage($data->GetMessage());
			}
		}
		return $data;
	}
}
