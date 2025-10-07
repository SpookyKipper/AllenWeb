<?php

namespace Allen\Basic\Util\Integration\Ai\Data;

use Allen\Basic\Util\Integration\Ai\Data\Message\{Content, Role};

class Message
{
	/**
	 * 處理並載入內容
	 * @param string|array $content
	 * @return Content[]
	 */
	protected static function LoadContent(string|array $content): array
	{
		if (is_string($content)) {
			$content = [
				$content,
			];
		}
		return array_map(
			fn($c) => is_string($c) ? Content::Text($c) : $c,
			array_filter(
				$content,
				fn($c) => $c instanceof Content || is_string($c),
			),
		);
	}
	/**
	 * @param Content[] $content
	 */
	public function __construct(
		protected Role $role,
		protected string|array $content = [],
	) {
		$this->content = self::LoadContent($content);
	}
	public function __serialize(): array
	{
		return [
			'role' => $this->role->value,
			'content' => array_map(fn($c) => serialize($c), $this->content),
		];
	}
	public function __unserialize(array $data): void
	{
		$this->role = Role::from($data['role']);
		$this->content = array_map(fn($c) => unserialize($c), $data['content'] ?? []);
	}
	public function ToArray(): array
	{
		return [
			'role' => $this->role->value,
			'content' => array_map(fn($c) => $c->ToArray(), $this->content),
		];
	}
	public function GetRole(): Role
	{
		return $this->role;
	}
	public function SetRole(Role $role): self
	{
		$this->role = $role;
		return $this;
	}
	public function GetContent(): array
	{
		return $this->content;
	}
	public function SetContent(string|array $content): self
	{
		$this->content = self::LoadContent($content);
		return $this;
	}
	public function AddContent(string|Content ...$content): self
	{
		$this->content = array_merge($this->content, self::LoadContent($content));
		return $this;
	}
	public function OpenAI(): array
	{
		return [
			'role' => $this->role->value,
			'content' => array_values(array_map(fn($c) => $c->OpenAI(), $this->content)),
		];
	}
	public function GoogleAI(): array
	{
		return [
			'role' => match ($this->role) {
				Role::Assistant => 'model',
				default => $this->role->value,
			},
			'parts' => array_values(array_map(fn($c) => $c->GoogleAI(), $this->content)),
		];
	}
	public static function FromArray(array $data): self
	{
		$role = Role::tryFrom($data['role'] ?? '') ?? Role::User;
		$content = $data['content'] ?? [];
		if (is_string($content)) {
			$content = [$content];
		}
		$content = array_map(fn($c) => Content::FromArray($c), $content);
		return new self(
			role: $role,
			content: $content,
		);
	}
	public static function User(string|array $content): self
	{
		return new self(
			role: Role::User,
			content: $content,
		);
	}
}
