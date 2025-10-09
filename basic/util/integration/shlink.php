<?php

namespace Allen\Basic\Util\Integration;

use Allen\Basic\Util\Config;
use Allen\Basic\Util\Json;
use Allen\Basic\Util\Request;

class Shlink
{
	public function __construct(
		protected readonly string $host,
		protected readonly string $api_key,
	) {}
	protected function Request(string $path, array $header = []): Request
	{
		return new Request(
			url: 'https://' . $this->host . '/rest/v3/' . ltrim($path, '/'),
			header: [
				...$header,
				'X-Api-Key: ' . $this->api_key,
			],
		);
	}
	public function ShortUrlCreate(
		string $long_url,
		?int $valid_since = null,
		?int $valid_until = null,
		?int $max_visits = null,
		array $tags = [],
		?string $title = null,
		?bool $crawlable = null,
		?bool $forward_query = null,
		?string $custom_slug = null,
		?string $path_prefix = null,
		?bool $find_if_exists = null,
		?string $domain = null,
		?int $short_code_length = null,
	): ?string {
		$body = array_filter([
			'longUrl' => $long_url,
			'validSince' => $valid_since ? date('c', $valid_since) : null,
			'validUntil' => $valid_until ? date('c', $valid_until) : null,
			'maxVisits' => (is_int($max_visits) && $max_visits > 0) ? $max_visits : null,
			'tags' => array_values(array_unique(array_filter(array_map(fn($v) => trim(strval($v)), $tags)))),
			'title' => $title,
			'crawlable' => $crawlable,
			'forwardQuery' => $forward_query,
			'customSlug' => $custom_slug,
			'pathPrefix' => $path_prefix,
			'findIfExists' => $find_if_exists,
			'domain' => $domain,
			'shortCodeLength' => (is_int($short_code_length) && $short_code_length >= 4) ? $short_code_length : null,
		]);
		if (empty($body['longUrl'])) {
			return null;
		}
		$result = $this->Request(
			path: 'short-urls',
		)->POST(Json::Encode($body));
		if ($result['code'] !== 200 || !is_string($result['response']['shortUrl'] ?? null)) {
			return null;
		}
		return $result['response']['shortUrl'];
	}
	public static function FromConfig(): ?self
	{
		Config::Init();
		$host = Config::Get('util.shlink.host', null);
		$api_key = Config::Get('util.shlink.api_key', null);
		if (empty($host) || empty($api_key)) {
			return null;
		}
		return new self(
			host: $host,
			api_key: $api_key,
		);
	}
}
