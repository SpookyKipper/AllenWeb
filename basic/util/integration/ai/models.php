<?php

namespace Allen\Basic\Util\Integration\Ai;

use Allen\Basic\Util\Integration\Ai;
use Allen\Basic\Util\Integration\Ai\Data\ApiType;

class Models
{
	public function __construct(
		protected readonly Ai $ai,
	) {}
	protected ?array $models = null;
	public function List(): ?array
	{
		if (!is_null($this->models)) {
			return $this->models;
		}
		$url = match ($this->ai->api_type) {
			ApiType::OpenAI => '/models',
			ApiType::GoogleAI => '/models',
			default => null,
		};
		if (is_null($url)) {
			return null;
		}
		$data = $this->ai->_RequestGET($url);
		if ($data['code'] !== 200) {
			return null;
		}
		switch ($this->ai->api_type) {
			case ApiType::OpenAI: {
					if (!isset($data['response']['data']) || !is_array($data['response']['data']) || count($data['response']['data']) === 0) {
						return null;
					}
					return $this->models = $data['response']['data'];
				}
			case ApiType::GoogleAI: {
					if (!isset($data['response']['models']) || !is_array($data['response']['models']) || count($data['response']['models']) === 0) {
						return null;
					}
					return $this->models = $data['response']['models'];
					break;
				}
		}
		return null;
	}
	public function Has(string $model): bool
	{
		$list = $this->List();
		if (is_null($list)) {
			return false;
		}
		$id_key = match ($this->ai->api_type) {
			ApiType::OpenAI => 'id',
			ApiType::GoogleAI => 'name',
			default => null,
		};
		if (is_null($id_key)) {
			return false;
		}
		$ids = array_column($list, $id_key);
		return in_array($model, $ids);
	}
}
