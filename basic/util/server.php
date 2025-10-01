<?php

namespace Allen\Basic\Util;

class Server
{
	public static function GetDomain(?string $domain = null, bool $idn = true): ?string
	{
		$domain ??= $_SERVER['HTTP_HOST'];
		if (is_null($domain)) return null;
		return $idn ? idn_to_utf8($domain) : $domain;
	}
	public static function GetDomainPath(?string $domain = null, bool $idn = true): ?array
	{
		$domain = self::GetDomain($domain, $idn);
		if (is_null($domain)) return null;
		return array_reverse(explode('.', $domain));
	}
	public static function GetHeaders(): array
	{
		return getallheaders() ?: [];
	}
	public static function GetHeader(string $name): ?string
	{
		$headers = self::GetHeaders();
		return $headers[$name] ?? null;
	}
}
