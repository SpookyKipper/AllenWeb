<?php

namespace Allen\Basic\Element\Trait;

trait Src
{
	public function SrcGet(): ?string
	{
		return $this->AttributeGet('src');
	}
	public function SrcSet(?string $src): self
	{
		$this->AttributeAdd('src', $src);
		return $this;
	}
}
