<?php

namespace Allen\Basic\Element;

class Div extends Element
{
	public function __construct(
		?string $content = null,
		?array $attribute = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
	) {
		parent::__construct(
			tag: 'div',
			content: $content,
			attribute: $attribute,
			id: $id,
			class: $class,
			style: $style,
		);
	}
}
