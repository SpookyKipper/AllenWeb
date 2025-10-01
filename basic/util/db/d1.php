<?php

namespace Allen\Basic\Util\Db;

use RenokiCo\L1\{D1\Pdo\D1Pdo, CloudflareD1Connector};

class D1 extends D1Pdo
{
	public function __construct(?string $name = null, ?string $user = null, ?string $pass = null)
	{
		parent::__construct(
			dsn: 'sqlite::memory:',
			connector: new CloudflareD1Connector(
				database: $name,
				token: $pass,
				accountId: $user,
			),
		);
	}
}
