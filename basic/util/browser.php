<?php

namespace Allen\Basic\Util;

class Browser
{
	private static ?array $browser = null;
	public static function Get(): ?array
	{
		if (self::$browser === null) {
			$data = get_browser(null, true);
			if ($data !== false) {
				self::$browser = $data;
			}
		}
		return self::$browser;
	}
	public static function GetName(): ?string
	{
		$browser = self::Get();
		if ($browser !== null && isset($browser['browser'])) {
			return $browser['browser'];
		}
		return null;
	}
	public static function IsSafari(): bool
	{
		$name = self::GetName();
		return $name === 'Safari';
	}
}
