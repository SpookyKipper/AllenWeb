<?php
require_once __DIR__ . '/../../../../../main.php';

use Allen\Basic\Util\Config;
?>
<?= implode(
	'',
	array_map(
		fn($preconnect) =>
		'<link rel="preconnect" href="' .
			(is_array($preconnect) ? array_shift($preconnect) : $preconnect) .
			'"' .
			(is_array($preconnect) ? ' ' . implode(' ', $preconnect) : '') .
			'>' .
			\PHP_EOL,
		Config::Get('web.start.link.preconnect', []),
	),
) ?>