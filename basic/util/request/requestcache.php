<?php

namespace Allen\Basic\Util\Request;

use Allen\Basic\Util\Cache;
use Allen\Basic\Util\Request;
use CurlHandle;

class RequestCache extends Request
{
	protected Cache $cache_class;
	protected ?array $cache = null;
	public function __construct(
		string $cacheId,
		int $cacheExpire = 0,
		?string $url = null,
		?string $data = null,
		array $header = [],
		?string $ua = null,
	) {
		parent::__construct(
			url: $url,
			data: $data,
			header: $header,
			ua: $ua,
		);
		$this->cache_class = new Cache($cacheId, expire: $cacheExpire);
		if ($this->cache_class->Exist()) {
			$this->cache = $this->cache_class->Get(force: true);
			if (isset($this->cache['header']['last-modified'][0]) && is_string($this->cache['header']['last-modified'][0])) $this->HeaderAdd('If-Modified-Since', $this->cache['header']['last-modified'][0]);
			if (isset($this->cache['header']['etag'][0]) && is_string($this->cache['header']['etag'][0])) $this->HeaderAdd('If-None-Match', $this->cache['header']['etag'][0]);
		}
	}
	public function _CurlEnd(CurlHandle $ch)
	{
		if ($this->cache !== null && $this->cache_class->IsValid() === true) {
			return $this->cache;
		}
		$response = parent::_CurlEnd($ch);
		if ($response['code'] === 304) {
			$response = $this->cache;
		}
		if ($response['code'] === 200) {
			$this->cache_class->Set($response);
		}
		return $response;
	}
}
