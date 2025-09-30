<?php

namespace Allen\Basic;

use Allen\Basic\Util\Config;

class Path
{
	public static function Root(string $path = ''): string
	{
		$result = Config::Get('path.root', __DIR__ . '/..') . '/' . $path;
		return $result;
	}
	public static function Cache(string $path = ''): string
	{
		return self::Root(path: Config::Get('path.cache', 'cache') . '/' . $path);
	}
}
