<?php

namespace Allen\Basic;

use Allen\Basic\Util\Config;

class Path
{
	public static function Root(string $path = ''): string
	{
		return __DIR__ . '/../' . $path;
	}
	public static function Cache(string $path = ''): string
	{
		Config::Init();
		return self::Root(path: Config::Get('path.cache', 'cache') . '/' . $path);
	}
	public static function Setting(string $path = ''): string
	{
		return self::Root(path: 'setting/' . $path);
	}
}
