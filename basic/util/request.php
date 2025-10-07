<?php

namespace Allen\Basic\Util;

use CurlHandle;

class Request
{
	protected string $url;
	/**
	 * 請求
	 * @param string|null $url 請求的 URL
	 * @param array<string, string|null> $header 請求的 Header
	 */
	public function __construct(
		?string $url = null,
		protected array $header = [],
	) {
		if ($url !== null) {
			$this->url = $url;
		}
	}
	/**
	 * 取得請求 URL
	 * @return string|null
	 */
	public function UrlGet(): ?string
	{
		return $this->url ?? null;
	}
	/**
	 * 設定請求 URL
	 * @param string $url 請求的 URL
	 */
	public function UrlSet(string $url): self
	{
		$this->url = $url;
		return $this;
	}
	/**
	 * 取得 Header
	 * @return array<string, string>
	 */
	public function HeaderGet(): array
	{
		return array_filter($this->header);
	}
	/**
	 * 新增 Header
	 * @param string $key Header 的 Key
	 * @param string|null $value Header 的 Value
	 */
	public function HeaderAdd(string $key, ?string $value): self
	{
		$this->header[$key] = $value;
		return $this;
	}
	/**
	 * 設定 Header
	 * @param array<string, string|null> $header
	 */
	public function HeaderSet(array $header): self
	{
		$this->header = $header;
		return $this;
	}
	/**
	 * 執行 GET 請求
	 * @return array{code: int, response: mixed, header: array<string, string[]>}
	 */
	public function GET(): array
	{
		$ch = $this->_CurlStart();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		return $this->_CurlEnd($ch);
	}
	/**
	 * 執行 POST 請求
	 * @param string|null $data 上傳的資料
	 * @return array{code: int, response: mixed, header: array<string, string[]>}
	 */
	public function POST(?string $data = null): array
	{
		$ch = $this->_CurlStart();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return $this->_CurlEnd($ch);
	}
	/**
	 * 執行 PUT 請求
	 * @param string|null $data 上傳的資料
	 * @return array{code: int, response: mixed, header: array<string, string[]>}
	 */
	public function PUT(?string $data = null)
	{
		$ch = $this->_CurlStart();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		return $this->_CurlEnd($ch);
	}
	/**
	 * 初始化 Curl 請求
	 * @return CurlHandle
	 */
	protected function _CurlStart(): CurlHandle
	{
		$header = $this->HeaderGet();
		$header = array_values(array_map(function ($key, $value) {
			return $key . ': ' . $value;
		}, array_keys($header), $header));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->UrlGet());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		return $ch;
	}
	/**
	 * 結束 Curl 請求
	 * @param CurlHandle $ch
	 * @return array{code: int, response: mixed, header: array<string, string[]>}
	 */
	protected function _CurlEnd(CurlHandle $ch): array
	{
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$headers) {
			$len = strlen($header);
			$pos = strpos($header, ':');
			if ($pos === false) {
				return $len;
			}
			$name = strtolower(substr($header, 0, $pos));
			$value = trim(substr($header, $pos + 1));
			if (!array_key_exists($name, $headers)) {
				$headers[$name] = [];
			}
			$headers[$name][] = $value;
			return $len;
		});
		$response = curl_exec($ch);
		$error = curl_error($ch);
		if (!empty($error) && $response === false) {
			$code = 0;
			$response = [
				'error' => $error,
			];
		} else {
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (json_validate($response)) {
				$response = json_decode($response, true);
			}
		}
		curl_close($ch);
		return [
			'code' => $code,
			'response' => $response,
			'header' => $headers,
		];
	}
}
