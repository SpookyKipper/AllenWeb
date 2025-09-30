<?php
use Allen\Basic\Util\Config;
?>
<?= implode(
	'',
	array_map(
		fn($script) =>
		'<script src="' .
			(is_array($script) ? array_shift($script) : $script) .
			'"' .
			(is_array($script) ? ' ' . implode(' ', $script) : '') .
			'></script>' .
			\PHP_EOL,
		Config::Get('web.start.link.script', []),
	),
) ?>