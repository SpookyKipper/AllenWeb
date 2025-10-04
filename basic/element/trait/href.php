<?php

namespace Allen\Basic\Element\Trait;

trait Href
{
	public function HrefGet(): ?string
	{
		return $this->AttributeGet('href');
	}
	public function HrefSet(?string $href): self
	{
		return $this->AttributeAdd('href', $href);
	}
}
