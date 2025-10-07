<?php

namespace Allen\Basic\Util\Integration;

use Allen\Basic\Util\Request;
use Allen\Basic\Util\Integration\Ai\{ChatCompletions, Models};
use Allen\Basic\Util\Integration\Ai\Data\ApiType;
use Allen\Basic\Util\Request\RequestStream;

class Ai
{
	public readonly Models $models;
	public function __construct(
		public readonly string $base_url = 'https://generativelanguage.googleapis.com/v1beta/openai',
		protected ?string $api_key = null,
		public readonly ApiType $api_type = ApiType::OpenAI,
	) {
		$this->models = new Models($this);
	}
	/**
	 * 建立請求物件
	 * @param string $path 請求的路徑
	 * @param array<string, string> $header 請求的 Header
	 * @param bool $stream 是否使用串流模式
	 * @return Request|RequestStream
	 */
	protected function _Request(string $path, array $header = [], bool $stream = false): Request|RequestStream
	{
		$url = $this->base_url . $path;
		if (!empty($this->api_key)) {
			switch ($this->api_type) {
				case ApiType::OpenAI: {
						$header['Authorization'] = 'Bearer ' . $this->api_key;
						break;
					}
				case ApiType::GoogleAI: {
						$header['x-goog-api-key'] = $this->api_key;
						break;
					}
			}
		}
		if ($stream) {
			return new RequestStream(
				url: $url,
				header: $header,
			);
		}
		return new Request(
			url: $url,
			header: $header,
		);
	}
	/**
	 * 處理回傳資料
	 * @param array{code: int, response: mixed, header: array<string, string[]>} $data
	 */
	protected static function _RequestData(array $data): array
	{
		if (is_array($data['response']) && isset($data['response'][0]) && count($data['response']) === 1) {
			$data['response'] = $data['response'][0];
		}
		if ($data['code'] !== 200) {
			if (isset($data['response']['error']['message'])) {
				$data['response']['error'] = $data['response']['error']['message'];
			}
		}
		return $data;
	}
	/**
	 * 處理 GET 資料
	 * @param string $path 請求的路徑
	 * @param array<string, string> $header 請求的 Header
	 * @param bool $stream 是否使用串流模式
	 */
	public function _RequestGET(string $path, array $header = [], bool $stream = false)
	{
		return self::_RequestData($this->_Request(path: $path, header: $header, stream: $stream)->GET());
	}
	/**
	 * 處理 POST 資料
	 * @param string $path 請求的路徑
	 * @param array<string, string> $header 請求的 Header
	 * @param string|null $body 請求的 Body
	 * @param bool $stream 是否使用串流模式
	 */
	public function _RequestPOST(string $path, array $header = [], ?string $body = null, bool $stream = false)
	{
		return self::_RequestData($this->_Request(path: $path, header: $header, stream: $stream)->POST($body));
	}
	public function ChatCompletions(string $model, array $messages = []): ChatCompletions
	{
		return new ChatCompletions(
			ai: $this,
			model: $model,
			messages: $messages,
		);
	}
	public static function GoogleAI(
		string $base_url = 'https://generativelanguage.googleapis.com/v1beta',
		?string $api_key = null,
	): self {
		return new self(
			base_url: $base_url,
			api_key: $api_key,
			api_type: ApiType::GoogleAI,
		);
	}
}
