<?php

namespace Allen\Basic\Util;

class Language
{
	public const LANGS = [
		'en-US' => 'English (US)',
		'zh-Hant-TW' => '正體中文(中國)',
		'zh-Hans-TW' => '简化字(中国)',
		'zh-Hant-HK' => '正體中文(港澳地區)',
		'zh-Hans-CN' => '简化字(大陆地區)',
		'ja' => '日本語',
	];
	public static function GetName(string $lang): ?string
	{
		return self::LANGS[$lang] ?? null;
	}
	protected static ?string $lang = null;
	public static function Get(): string
	{
		if (is_null(self::$lang)) {
			$lang_support = self::GetSupport();
			if (!empty($lang_support)) {
				if (isset($_REQUEST['lang'])) {
					if (!is_string($_REQUEST['lang']) || $_REQUEST['lang'] === Config::Get('util.language.default', 'zh-Hant-TW') || !self::Set($_REQUEST['lang'])) {
						$uri = Uri::Parse($_SERVER['REQUEST_URI']);
						header('Location: ' . $uri->RemoveQuery('lang')->Get());
						die();
					}
				} else if (in_array(Config::Get('util.language.default', 'zh-Hant-TW'), $lang_support)) {
					self::Set(Config::Get('util.language.default', 'zh-Hant-TW'));
				} else {
					self::Set($lang_support[array_key_first($lang_support)]);
				}
			} else {
				self::Set(Config::Get('util.language.default', 'zh-Hant-TW'));
			}
		}
		return self::$lang;
	}
	public static function Set(string $lang): bool
	{
		if (in_array($lang, self::GetSupport())) {
			self::$lang = $lang;
			return true;
		}
		return false;
	}
	protected static ?array $lang_support = null;
	public static function GetSupport(): array
	{
		if (is_null(self::$lang_support)) {
			self::SetSupport();
		}
		return self::$lang_support;
	}
	public static function SetSupport(string ...$langs): void
	{
		$lang_support = array_values(array_intersect($langs, array_keys(self::LANGS)));
		if (empty($lang_support)) {
			$lang_support = [
				Config::Get('util.language.default', 'zh-Hant-TW'),
			];
		}
		self::$lang_support = $lang_support;
	}
	public static function Output(array $data): mixed
	{
		$lang = self::Get();
		if (array_key_exists($lang, $data)) {
			return $data[$lang];
		}
		$lang_support = self::GetSupport();
		$data = array_filter($data, function ($key) use ($lang_support) {
			return in_array($key, $lang_support);
		}, ARRAY_FILTER_USE_KEY);
		if (empty($data)) {
			return null;
		}
		uasort($data, function ($a, $b) use ($lang_support) {
			$index_a = array_search($a, $lang_support);
			$index_b = array_search($b, $lang_support);
			return $index_a <=> $index_b;
		});
		$lang_split = explode('-', $lang);
		$data1 = array_filter($data, function ($key) use ($lang_split) {
			return str_starts_with($key, $lang_split[0]);
		}, ARRAY_FILTER_USE_KEY);
		if (count($data1) > 0) {
			if (count($lang_split) > 1) {
				$data2 = array_filter($data, function ($key) use ($lang_split) {
					return str_starts_with($key, $lang_split[0] . '-' . $lang_split[1]);
				}, ARRAY_FILTER_USE_KEY);
				if (count($data2) > 0) {
					return $data2[array_key_first($data2)];
				}
			}
			return $data1[array_key_first($data1)];
		}
		return $data[array_key_first($data)];
	}
	public static function YearOffset(): int
	{
		return match (self::Get()) {
			'zh-Hant-TW' => 1911,
			default => 0,
		};
	}
}
