<?php

namespace Allen\Basic\Element;

use Allen\Basic\Element\Trait\{Href, Target};
use Allen\Basic\Element\Enum\Target as EnumTarget;

class A extends Element
{
	use Href;
	use Target;
	public function __construct(
		?array $attribute = null,
		?string $content = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
		?string $href = null,
		string|EnumTarget|null $target = null,
	) {
		parent::__construct(
			tag: 'a',
			attribute: $attribute,
			content: $content,
			id: $id,
			class: $class,
			style: $style,
		);
		if (!is_null($href)) $this->HrefSet($href);
		if (!is_null($target)) $this->TargetSet($target);
	}
}
