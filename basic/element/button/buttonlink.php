<?php

namespace Allen\Basic\Element\Button;

use Allen\Basic\Element\{A, Button};
use Allen\Basic\Element\Enum\Target;

class ButtonLink extends Button
{
	protected A $a;
	public function __construct(
		?array $attribute = null,
		?string $content = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
		?string $href = null,
		string|Target|null $target = null,
	) {
		parent::__construct(
			attribute: $attribute,
			content: $content,
			id: $id,
			class: $class,
			style: $style,
		);
		$this->a = new A(
			href: $href,
			target: $target,
		);
	}
	public function Render(): string
	{
		$render = parent::Render();
		$this->a->ContentSet($render);
		return $this->a->Render();
	}
}
