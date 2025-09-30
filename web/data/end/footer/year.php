<?php
require_once __DIR__ . '/../../../../../main.php';

use Allen\Basic\Util\Config;
?>
<div>
	<p>&copy;<?= date('Y') - 1911 ?> <?= Config::Get('web.end.author', 'AS_Allen_Shih') ?></p>
</div>