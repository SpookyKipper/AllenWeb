<?php

namespace Allen\Basic\Element\Trait;

use Allen\Basic\Element\Enum\Rel as EnumRel;

trait Rel
{
	use Element;
	public function RelGet(): ?EnumRel
	{
		$rel = $this->AttributeGet('rel');
		return !is_null($rel) ? EnumRel::tryFrom($rel) : null;
	}
	public function RelSet(?EnumRel $rel): self
	{
		return $this->AttributeAdd('rel', $rel?->value);
	}
}
