<?php

namespace Allen\Basic\Util;

use Allen\Basic\Util\Db\{MySQL, D1};
use Exception;

class Db
{
	/**
	 * @var MySQL|D1[]
	 */
	private static $instances = [];
	public static function Get(string $id = 'default'): MySQL|D1
	{
		if (!isset(self::$instances[$id])) {
			$type = Config::Get('util.db.' . $id . '.type', 'mysql');
			$host = Config::Get('util.db.' . $id . '.host', 'localhost');
			$name = Config::Get('util.db.' . $id . '.name', 'default');
			$user = Config::Get('util.db.' . $id . '.user', null);
			$pass = Config::Get('util.db.' . $id . '.pass', null);
			$options = Config::Get('util.db.' . $id . '.options', []);
			self::$instances[$id] = match (strtolower($type)) {
				'mysql' => new MySQL($host, $name, $user, $pass, $options),
				'd1' => new D1($name, $user, $pass),
				default => throw new Exception('Unsupported database type: ' . $type),
			};
		}
		return self::$instances[$id];
	}
}
