<?php

namespace Allen\Basic\Element\Trait;

use Allen\Basic\Element\Enum\ButtonType as EnumButtonType;

trait ButtonType
{
	use Element;
	public function TypeGet(): ?EnumButtonType
	{
		$type = $this->AttributeGet('type');
		return !is_null($type) ? EnumButtonType::tryFrom($type) : null;
	}
	public function TypeSet(string|EnumButtonType $type): self
	{
		return $this->AttributeAdd('type', $type instanceof EnumButtonType ? $type->value : $type);
	}
}