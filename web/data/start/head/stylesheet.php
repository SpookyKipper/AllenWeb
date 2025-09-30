<?php
use Allen\Basic\Util\Config;
?>
<link rel="stylesheet" href="https://cdn.asallenshih.tw/style/<?= Config::Get('web.start.link.web_style', 'allen') ?>.css">
<?= implode(
	'',
	array_map(
		fn($stylesheet) =>
		'<link rel="stylesheet" href="' .
			$stylesheet .
			'">' .
			\PHP_EOL,
		Config::Get('web.start.link.stylesheet', []),
	),
) ?>