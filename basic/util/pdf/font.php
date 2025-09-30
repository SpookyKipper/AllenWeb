<?php

namespace Allen\Basic\Util\Pdf;

class Font {
	public function __construct(
		public string $name,
		public string $regular_file,
		public ?string $bold_file = null,
		public ?string $italic_file = null,
		public ?string $bold_italic_file = null,
		public ?string $lang = null,
		public ?string $script = null,
		public ?string $country = null,
	) {}
}