<?php

namespace Allen\Basic\Element;

use Allen\Basic\Element\Trait\{ScriptType, Src};

class Script extends Element
{
	use Src, ScriptType;
	public function __construct(
		?string $src = null,
		?ScriptType $type = null,
		?string $content = null,
	) {
		parent::__construct(
			tag: 'script',
			content: $content,
		);
		if (!is_null($src)) $this->SrcSet($src);
		if (!is_null($type)) $this->TypeSet($type);
	}
}
