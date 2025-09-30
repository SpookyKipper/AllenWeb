<?php

namespace Allen\Basic\Util\Request;

use Allen\Basic\Util\Cache;
use Allen\Basic\Util\Request;

class RequestCache extends Request
{
	protected Cache $cache_class;
	protected ?array $cache = null;
	public function __construct(string $cacheId, int $cacheExpire = 0, ?string $url = null, array $header = [])
	{
		parent::__construct($url, $header);
		$this->cache_class = new Cache($cacheId, expire: $cacheExpire);
		if ($this->cache_class->Exist()) {
			$this->cache = $this->cache_class->Get(force: true);
			if (isset($this->cache['header'][0]['last-modified']) && is_string($this->cache['header'][0]['last-modified'])) {
				$this->HeaderAdd('If-Modified-Since', $this->cache['header'][0]['last-modified']);
			}
		}
	}
	protected function _CurlEnd(\CurlHandle $ch): array
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
