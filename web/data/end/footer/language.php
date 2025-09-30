<?php
use Allen\Basic\Util\Language;
use Allen\Basic\Util\Uri;
?>
<div>
	<p>
		<label>
			<span class="material-symbols-outlined">language</span>
			<select class="max-width" onchange="location = `${this.value}`;">
				<?php foreach (Language::GetSupport() as $lang) { ?>
					<option <?= $lang === Language::Get() ? 'selected ' : '' ?>value="<?= $lang === 'zh-Hant-TW' ? Uri::Parse($_SERVER['REQUEST_URI'])->RemoveQuery('lang')->Get() : Uri::Parse($_SERVER['REQUEST_URI'])->AddQuery('lang', $lang)->Get() ?>"><?= Language::GetName($lang) ?></option>
				<?php } ?>
			</select>
		</label>
	</p>
</div>