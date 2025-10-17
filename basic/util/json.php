<?php

namespace Allen\Basic\Util;

class Json
{
	static public function Validate(string $json): bool
	{
		return json_validate($json);
	}
	static public function Encode(mixed $data, bool $pretty = false): string
	{
		return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | ($pretty ? JSON_PRETTY_PRINT : 0));
	}
	static public function Decode(string $json): mixed
	{
		return json_decode($json, true);
	}
	static public function Output(mixed $data, bool $pretty = false, bool $etag = true, ?int $last_modified = null): never
	{
		$output = self::Encode($data, $pretty);
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Length: ' . strlen($output));
		if (is_int($last_modified)) {
			header('Last-Modified: ' . date('r', $last_modified));
			$if_modified_since = Server::GetHeader('If-Modified-Since');
			if (is_string($if_modified_since) && strtotime($if_modified_since) >= $last_modified) {
				http_response_code(304);
				exit;
			}
		}
		if ($etag) {
			$sha1 = sha1($output);
			header('ETag: "' . $sha1 . '"');
			$if_none_match = Server::GetHeader('If-None-Match');
			if (is_string($if_none_match) && $if_none_match === $sha1) {
				http_response_code(304);
				exit;
			}
		}
		echo $output;
		exit;
	}
}
