<?php

namespace Allen\Util\Db;

use Exception;
use Allen\Basic\Util\Db;
use Allen\Basic\Util\Db\MySQL as DbMySQL;
use Allen\Util\Db\Session\MySQL;

class Session
{
	static public function Get(string $id = 'default'): MySQL
	{
		$get = Db::Get($id);
		if ($get instanceof DbMySQL) {
			return new MySQL($get);
		}
		throw new Exception('Unsupported database type for session: ' . get_class($get));
	}
}
