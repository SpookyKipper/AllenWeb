<?php

namespace Allen\Basic\Util;

use InvalidArgumentException;

class Config
{
	protected static array $config_type = [];
	protected static array $config_replace = [];
	protected static array $config = [];
	public static function SetType(string $key, ConfigType ...$type): void
	{
		self::$config_type[$key] = $type;
	}
	public static function GetType(string $key): ?array
	{
		return self::$config_type[$key] ?? null;
	}
	public static function CheckType(string $key, string $value_type): bool
	{
		if (isset(self::$config_type[$key]) && is_array(self::$config_type[$key])) {
			$allow_type = self::$config_type[$key];
			if (!in_array($value_type, array_map(fn($type) => $type->value, $allow_type)) && !in_array(ConfigType::Mixed->value, $allow_type) && ConfigType::Null->value !== $value_type) {
				return false;
			}
		}
		return true;
	}
	public static function LoadType(string $dir, string $name): void
	{
		if (is_dir($dir . '/' . $name)) {
			array_map(fn($file) => require_once $file, glob($dir . '/' . $name . '/*.php'));
		}
	}
	public static function Get(string $key, mixed $default = null): mixed
	{
		return self::$config_replace[$key] ?? self::$config[$key] ?? $default;
	}
	public static function Set(string $key, mixed $value = null, bool $replace = false, bool $is_add = false, ?string $path = null): void
	{
		if (!is_null($path) && (!isset($_SERVER['REQUEST_URI']) || !str_starts_with($_SERVER['REQUEST_URI'], $path))) {
			return;
		}
		$value_type = gettype($value);
		if (self::CheckType($key, $value_type) === false) {
			throw new InvalidArgumentException('Invalid type for ' . $key . ': ' . $value_type . ', expected: ' . implode(
				', ',
				array_map(
					fn($type) => $type->value,
					self::GetType($key) ?? [],
				),
			));
		}
		if ($is_add && null !== $orginal_value = $replace ? (self::$config_replace[$key] ?? null) : (self::$config[$key] ?? null)) {
			$orginal_value_type = gettype($orginal_value);
			if (($value_type === 'integer' || $value_type === 'double') && ($orginal_value_type === 'integer' || $orginal_value_type === 'double')) {
				$value += $orginal_value;
			} else if ($value_type === 'string' && $orginal_value_type === 'string') {
				$value = $orginal_value . $value;
			} else if ($value_type === 'array' && $orginal_value_type === 'array') {
				$value = array_merge($orginal_value, $value);
			} else {
				throw new InvalidArgumentException('Cannot add value for type ' . $value_type . ' at key ' . $key);
			}
		}
		if ($replace) {
			self::$config_replace[$key] = $value;
		} else {
			self::$config[$key] = $value;
		}
	}
	protected static bool $inited = false;
	public static function Init(): void
	{
		if (self::$inited) {
			return;
		}
		self::$inited = true;
		self::LoadType(__DIR__, 'config');
		$domain_path = Server::GetDomainPath();
		if (is_null($domain_path)) {
			return;
		}
		$domain_path = [
			'default',
			...array_map(fn($i) => implode('.', array_slice($domain_path, 0, $i + 1)), array_keys($domain_path)),
		];
		$config_base = __DIR__ . '/../../setting/config/';
		foreach ($domain_path as $domain) {
			$config_file = $config_base . $domain . '.php';
			if (is_file($config_file)) {
				require_once $config_file;
			}
		}
	}
}
