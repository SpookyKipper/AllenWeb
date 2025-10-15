<?php

namespace Allen\Basic\Element\Custom;

trait CarouselItemTrait
{
	protected ?string $background = null;
	protected ?float $background_opacity = null;
	public function BackgroundGet(): ?string
	{
		return $this->background;
	}
	public function BackgroundSet(?string $background): self
	{
		$this->background = $background;
		return $this;
	}
	public function BackgroundOpacityGet(): ?float
	{
		return $this->background_opacity;
	}
	public function BackgroundOpacitySet(?float $background_opacity): self
	{
		$this->background_opacity = $background_opacity;
		return $this;
	}
}
