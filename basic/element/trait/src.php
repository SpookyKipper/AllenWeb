<?php

namespace Allen\Basic\Element\Trait;

trait Src
{
	use Element;
	public function SrcGet(): ?string
	{
		return $this->AttributeGet('src');
	}
	public function SrcSet(?string $src): self
	{
		return $this->AttributeAdd('src', $src);
	}
}
