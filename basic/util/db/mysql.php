<?php

namespace Allen\Basic\Util\Db;

use PDO;

class MySQL extends PDO
{
	public function __construct(string $host, string $name, ?string $user = null, ?string $pass = null, array $options = [])
	{
		parent::__construct(
			'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4',
			$user,
			$pass,
			[
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				...$options,
			]
		);
	}
}
