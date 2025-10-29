<?php

namespace Allen;

use Allen\Basic\Util\{Config, Header};

class Web
{
	public static function Start(
		bool $etag = true,
	): void {
		global $title, $description;
		if ($etag === true) ob_start(function(string $content) {
			Header::ETag($content);
			return $content;
		});
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
