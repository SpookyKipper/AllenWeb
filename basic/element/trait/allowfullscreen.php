<?php

namespace Allen\Basic\Element\Trait;

trait AllowFullscreen
{
	public function AllowfullscreenGet(): ?string
	{
		return $this->AttributeHas('allowfullscreen');
	}
	public function AllowfullscreenSet(bool $allowfullscreen = false): self
	{
		if ($allowfullscreen) {
			$this->AttributeAdd('allowfullscreen');
		} else {
			$this->AttributeRemove('allowfullscreen');
		}
		return $this;
	}
}
