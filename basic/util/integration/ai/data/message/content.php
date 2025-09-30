<?php

namespace Allen\Basic\Util\Integration\Ai\Data\Message;

use Allen\Basic\Util\Integration\Ai\Data\Message\Content\Type;

class Content
{
	const Type = Type::class;
	public function __construct(
		protected Type $type,
		protected ?string $text = null,
	) {}
	public function ToArray(): array
	{
		return array_filter([
			'type' => $this->type->value,
			'text' => $this->text,
		]);
	}
	public function OpenAI(): array
	{
		return [
			'type' => $this->type->value,
			...match ($this->type) {
				Type::Text => [
					'text' => $this->text,
				],
				default => [],
			},
		];
	}
	public function GoogleAI(): array
	{
		return match ($this->type) {
			Type::Text => [
				'text' => $this->text,
			],
			default => [],
		};
	}
	public static function FromArray(string|array $data): self
	{
		if (is_string($data)) {
			return self::Text($data);
		}
		return new self(
			type: Type::from($data['type']),
			text: $data['text'] ?? null,
		);
	}
	public static function Text(string $text): self
	{
		return new self(
			type: Type::Text,
			text: $text,
		);
	}
}
