<div id="menu">
	<input class="hidden" type="checkbox" id="menuControl">
	<label for="menuControl">
		<span class="material-symbols-outlined menuOpen">
			menu
		</span>
		<span class="material-symbols-outlined menuClose">
			close
		</span>
	</label>
	<?php

	use Allen\Basic\Util\{Menu, Language};

	echo Menu::Output(Language::Output(Menu::Get()));
	?>
</div>