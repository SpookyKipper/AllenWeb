<?php
use Allen\Basic\Util\{Config, Language};
?>
<div>
	<p>&copy;<?= date('Y') - Language::YearOffset() ?> <?= Config::Get('web.end.author', 'AS_Allen_Shih') ?></p>
</div>