<?php

namespace Allen\Basic\Element\Trait;

use Allen\Basic\Element\Enum\Target as EnumTarget;

trait Target
{
	public function TargetGet(): ?EnumTarget
	{
		$target = $this->AttributeGet('target');
		return !is_null($target) ? EnumTarget::tryFrom($target) : null;
	}
	public function TargetSet(string|EnumTarget $target): self
	{
		return $this->AttributeAdd('target', $target instanceof EnumTarget ? $target->value : $target);
	}
}
