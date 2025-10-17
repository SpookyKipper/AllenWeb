<?php

namespace Allen\Basic\Util\Request;

use Allen\Basic\Util\Request;
use CurlHandle;

class RequestStream extends Request
{
	public $callback;
	/**
	 * 請求串流
	 * @param string|null $url 請求的 URL
	 * @param array<string, string|null> $header 請求的 Header
	 * @param callable{CurlHandle, string} $callback
	 */
	public function __construct(
		?string $url = null,
		?string $data = null,
		array $header = [],
		?string $ua = null,
		?callable $callback = null,
	) {
		parent::__construct(
			url: $url,
			data: $data,
			header: $header,
			ua: $ua,
		);
		$this->callback = $callback;
	}
	public function _CurlStart(
		?string $method = null,
		?string $data = null,
		?string $url = null,
		array $header = [],
		?string $ua = null,
	): CurlHandle {
		$ch = parent::_CurlStart(
			method: $method,
			data: $data,
			url: $url,
			header: $header,
			ua: $ua,
		);
		if (is_callable($this->callback)) {
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
				@($this->callback)($ch, $data);
				return strlen($data);
			});
		}
		return $ch;
	}
}
