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
		array $header = [],
		?callable $callback = null,
	) {
		parent::__construct(
			url: $url,
			header: $header,
		);
		$this->callback = $callback;
	}
	protected function _CurlStart(): CurlHandle
	{
		$ch = parent::_CurlStart();
		if (is_callable($this->callback)) {
			curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
				@($this->callback)($ch, $data);
				return strlen($data);
			});
		}
		return $ch;
	}
}
