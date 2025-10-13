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
	static public function Output(mixed $data, bool $pretty = false): never
	{
		$output = self::Encode($data, $pretty);
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Length: ' . strlen($output));
		echo $output;
		exit;
	}
}
