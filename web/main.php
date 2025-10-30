<?php

namespace Allen;

use Allen\Basic\Util\{Config, Header};

class Web
{
	public static function Start(
		bool $etag = true,
		bool $cache = true,
		?bool $cache_public = null,
		?bool $cache_no_cache = null,
		?bool $cache_no_store = null,
		?int $cache_max_age = null,
		?bool $cache_must_revalidate = null,
	): void {
		global $title, $description;
		if ($etag === true) ob_start(function (string $content) {
			Header::ETag($content);
			return $content;
		});
		if ($cache === true) Header::CacheControl(
			public: $cache_public,
			no_cache: $cache_no_cache,
			no_store: $cache_no_store,
			max_age: $cache_max_age,
			must_revalidate: $cache_must_revalidate,
		);
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
