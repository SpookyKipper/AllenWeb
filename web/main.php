<?php

namespace Allen;

use Allen\Basic\Util\Config;

class Web
{
	public static function Start(): void
	{
		global $title, $description;
		require_once __DIR__ . '/data/start.php';
	}
	public static function End(): void
	{
		global $script;
		require_once __DIR__ . '/data/end.php';
	}
	public static function Config(): void
	{
		Config::Init();
	}
}
