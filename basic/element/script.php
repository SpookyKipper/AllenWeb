<?php

namespace Allen\Basic\Element;

use Allen\Basic\Element\Enum\ScriptType as EnumScriptType;
use Allen\Basic\Element\Trait\{ScriptType, Src};

class Script extends Element
{
	use Src, ScriptType;
	public function __construct(
		?string $src = null,
		?EnumScriptType $type = null,
		?string $content = null,
	) {
		parent::__construct(
			tag: 'script',
			content: $content,
		);
		if (!is_null($src)) $this->SrcSet($src);
		if (!is_null($type)) $this->TypeSet($type);
	}
	public static function Module(
		?string $src = null,
		?string $content = null,
	): self {
		$script = new self(
			src: $src,
			type: EnumScriptType::Module,
			content: $content,
		);
		return $script;
	}
}
