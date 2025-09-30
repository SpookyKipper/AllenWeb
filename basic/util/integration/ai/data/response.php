<?php

namespace Allen\Basic\Util\Integration\Ai\Data;

use Allen\Basic\Util\Integration\Ai\Data\{Message, Message\Content\Type, Message\Role};

class Response
{
	public function __construct(
		protected ?int $code = null,
		protected ?string $error = null,
		protected ?Message $message = null,
		protected ?int $created = null,
		protected ?string $id = null,
		protected ?string $model = null,
		protected ?string $object = null,
		protected ?int $usage_completion_tokens = null,
		protected ?int $usage_prompt_tokens = null,
		protected ?int $usage_total_tokens = null,
	) {}
	public function __serialize(): array
	{
		return array_filter([
			'code' => $this->code,
			'error' => $this->error,
			'message' => is_null($this->message) ? null : serialize($this->message),
			'created' => $this->created,
			'id' => $this->id,
			'model' => $this->model,
			'object' => $this->object,
			'usage_completion_tokens' => $this->usage_completion_tokens,
			'usage_prompt_tokens' => $this->usage_prompt_tokens,
			'usage_total_tokens' => $this->usage_total_tokens,
		]);
	}
	public function __unserialize(array $data): void
	{
		$this->code = $data['code'] ?? null;
		$this->error = $data['error'] ?? null;
		$this->message = isset($data['message']) ? unserialize($data['message']) : null;
		$this->created = $data['created'] ?? null;
		$this->id = $data['id'] ?? null;
		$this->model = $data['model'] ?? null;
		$this->object = $data['object'] ?? null;
		$this->usage_completion_tokens = $data['usage_completion_tokens'] ?? null;
		$this->usage_prompt_tokens = $data['usage_prompt_tokens'] ?? null;
		$this->usage_total_tokens = $data['usage_total_tokens'] ?? null;
	}
	public function ToArray(): array
	{
		return array_filter([
			'code' => $this->code,
			'error' => $this->error,
			'message' => is_null($this->message)
				? null
				: $this->message->ToArray(),
			'created' => $this->created,
			'id' => $this->id,
			'model' => $this->model,
			'object' => $this->object,
			'usage_completion_tokens' => $this->usage_completion_tokens,
			'usage_prompt_tokens' => $this->usage_prompt_tokens,
			'usage_total_tokens' => $this->usage_total_tokens,
		]);
	}
	public function GetCode(): ?int
	{
		return $this->code;
	}
	public function GetError(): ?string
	{
		return $this->error;
	}
	public function GetMessage(): ?Message
	{
		return $this->message;
	}
	public function GetCreated(): ?int
	{
		return $this->created;
	}
	public function GetId(): ?string
	{
		return $this->id;
	}
	public function GetModel(): ?string
	{
		return $this->model;
	}
	public function GetObject(): ?string
	{
		return $this->object;
	}
	public function GetUsageCompletionTokens(): ?int
	{
		return $this->usage_completion_tokens;
	}
	public function GetUsagePromptTokens(): ?int
	{
		return $this->usage_prompt_tokens;
	}
	public function GetUsageTotalTokens(): ?int
	{
		return $this->usage_total_tokens;
	}
	public static function FromArray(array $data): ?self
	{
		$code = $data['code'] ?? null;
		$error = $data['error'] ?? null;
		$message = isset($data['message']) && is_array($data['message']) ? Message::FromArray($data['message']) : null;
		$created = $data['created'] ?? null;
		$id = $data['id'] ?? null;
		$model = $data['model'] ?? null;
		$object = $data['object'] ?? null;
		$usage_completion_tokens = $data['usage_completion_tokens'] ?? null;
		$usage_prompt_tokens = $data['usage_prompt_tokens'] ?? null;
		$usage_total_tokens = $data['usage_total_tokens'] ?? null;
		if (empty(array_filter([
			$code,
			$error,
			$message,
			$created,
			$id,
			$model,
			$object,
			$usage_completion_tokens,
			$usage_prompt_tokens,
			$usage_total_tokens,
		]))) {
			return null;
		}
		return new self(
			code: $code,
			error: $error,
			message: $message,
			created: $created,
			id: $id,
			model: $model,
			object: $object,
			usage_completion_tokens: $usage_completion_tokens,
			usage_prompt_tokens: $usage_prompt_tokens,
			usage_total_tokens: $usage_total_tokens,
		);
	}
	public static function OpenAI(?array $data): array
	{
		return array_filter([
			'code' => $data['code'] ?? null,
			'error' => $data['response']['error'] ?? null,
			'message' => isset($data['response']['choices'][0]['message']) ? $data['response']['choices'][0]['message'] : null,
			'created' => $data['response']['created'] ?? null,
			'id' => $data['response']['id'] ?? null,
			'model' => $data['response']['model'] ?? null,
			'object' => $data['response']['object'] ?? null,
			'usage_completion_tokens' => $data['response']['usage']['completion_tokens'] ?? null,
			'usage_prompt_tokens' => $data['response']['usage']['prompt_tokens'] ?? null,
			'usage_total_tokens' => $data['response']['usage']['total_tokens'] ?? null,
		]);
	}
	public static function GoogleAI_Message_Content(?array $data): ?array
	{
		if (is_null($data)) {
			return null;
		}
		$type = array_keys($data);
		if (in_array('text', $type)) {
			return [
				'type' => Type::Text->value,
				'text' => $data['text'],
			];
		}
		return null;
	}
	public static function GoogleAI_Message(?array $data): ?array
	{
		if (is_null($data)) {
			return null;
		}
		return array_filter([
			'role' => (match ($data['role']) {
				'model' => Role::Assistant,
				default => Role::from($data['role']),
			})->value,
			'content' => isset($data['parts']) && is_array($data['parts'])
				? array_map(fn($c) => self::GoogleAI_Message_Content($c), $data['parts'])
				: null,
		]);
	}
	public static function GoogleAI(?array $data): array
	{
		return array_filter([
			'code' => $data['code'] ?? null,
			'message' => isset($data['response']['candidates'][0]['content']) ? self::GoogleAI_Message($data['response']['candidates'][0]['content']) : null,
			'usage_prompt_tokens' => $data['response']['usageMetadata']['promptTokenCount']	?? null,
			'usage_total_tokens' => $data['response']['usageMetadata']['totalTokenCount'] ?? null,
		]);
	}
	public static function FromOpenAI(?array $data): ?self
	{
		return self::FromArray(self::OpenAI($data));
	}
	public static function FromGoogleAI(?array $data): ?self
	{
		return self::FromArray(self::GoogleAI($data));
	}
}
