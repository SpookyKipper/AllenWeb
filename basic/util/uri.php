<?php

namespace Allen\Basic\Util;

use Allen\Basic\Util\{Config, Language};

class Uri
{
	protected ?string $protocol = null;
	protected ?string $user = null;
	protected ?string $password = null;
	protected ?string $host = null;
	protected ?int $port = null;
	protected ?string $path = null;
	protected ?array $query = null;
	protected ?string $fragment = null;
	public function __construct(
		?string $protocol = null,
		?string $user = null,
		?string $password = null,
		?string $host = null,
		?int $port = null,
		?string $path = null,
		?array $query = null,
		?string $fragment = null
	) {
		$this
			->SetProtocol($protocol)
			->SetUser($user)
			->SetPassword($password)
			->SetHost($host)
			->SetPort($port)
			->SetPath($path)
			->SetQuery($query)
			->SetFragment($fragment);
	}
	public function GetProtocol(): ?string
	{
		return $this->protocol;
	}
	public function SetProtocol(?string $protocol): self
	{
		$this->protocol = $protocol;
		return $this;
	}
	public function GetUser(): ?string
	{
		return $this->user;
	}
	public function SetUser(?string $user): self
	{
		$this->user = $user;
		return $this;
	}
	public function GetPassword(): ?string
	{
		return $this->password;
	}
	public function SetPassword(?string $password): self
	{
		$this->password = $password;
		return $this;
	}
	public function GetHost(): ?string
	{
		return $this->host;
	}
	public function SetHost(?string $host): self
	{
		$this->host = $host;
		return $this;
	}
	public function GetPort(): ?int
	{
		return $this->port;
	}
	public function SetPort(?int $port): self
	{
		if (!is_null($port) && ($port < 0 || $port > 65535)) {
			throw new \InvalidArgumentException('Port must be between 0 and 65535.');
		}
		$this->port = $port;
		return $this;
	}
	public function GetPath(): ?string
	{
		return $this->path;
	}
	public function SetPath(?string $path): self
	{
		$this->path = $path;
		return $this;
	}
	public function GetQuery(): ?array
	{
		return $this->query;
	}
	public function SetQuery(?array $query): self
	{
		$this->query = $query;
		return $this;
	}
	public function AddQuery(string $key, ?string $value): self
	{
		if (is_null($this->query)) {
			$this->query = [];
		}
		$this->query[$key] = $value;
		return $this;
	}
	public function RemoveQuery(string $key): self
	{
		if (!is_null($this->query) && array_key_exists($key, $this->query)) {
			unset($this->query[$key]);
			if (empty($this->query)) {
				$this->query = null;
			}
		}
		return $this;
	}
	public function GetFragment(): ?string
	{
		return $this->fragment;
	}
	public function SetFragment(?string $fragment): self
	{
		$this->fragment = $fragment;
		return $this;
	}
	public function Get(): ?string
	{
		$uri = '';
		if (!is_null($this->GetProtocol())) {
			$uri .= $this->GetProtocol() . '://';
		}
		if (!is_null($this->GetUser())) {
			$uri .= $this->GetUser();
			if (!is_null($this->GetPassword())) {
				$uri .= ':' . $this->GetPassword();
			}
			$uri .= '@';
		}
		if (!is_null($this->GetHost())) {
			$uri .= $this->GetHost();
		}
		if (!is_null($this->GetPort())) {
			$uri .= ':' . $this->GetPort();
		}
		if (!is_null($this->GetPath())) {
			if (!empty($uri) && !str_starts_with($this->GetPath(), '/')) {
				$uri .= '/';
			}
			$uri .= $this->GetPath();
		}
		if (!is_null($this->GetQuery())) {
			$uri .= '?';
			if (!empty($this->GetQuery())) {
				$uri .= http_build_query($this->GetQuery(), '', '&', PHP_QUERY_RFC3986);
			}
		}
		if (!is_null($this->GetFragment())) {
			$uri .= '#' . $this->GetFragment();
		}
		return $uri;
	}
	public static function ParseQuery(string $query): array
	{
		$parsedQuery = [];
		parse_str($query, $parsedQuery);
		return $parsedQuery;
	}
	public static function Parse(string $uri): self
	{
		$parsed = parse_url($uri);
		return new self(
			$parsed['scheme'] ?? null,
			$parsed['user'] ?? null,
			$parsed['pass'] ?? null,
			$parsed['host'] ?? null,
			isset($parsed['port']) ? (int)$parsed['port'] : null,
			$parsed['path'] ?? null,
			isset($parsed['query']) ? self::ParseQuery($parsed['query']) : null,
			$parsed['fragment'] ?? null
		);
	}
	/**
	 * 修改連結網址
	 */
	public static function Link(string $url, bool $lang = false): string
	{
		$uri = self::Parse($url);
		if ($lang) {
			if (Language::Get() === Config::Get('util.language.default', 'zh-Hant-TW')) {
				$uri = $uri->RemoveQuery('lang');
			} else {
				$uri = $uri->AddQuery('lang', Language::Get());
			}
		}
		return $uri->Get();
	}
}
