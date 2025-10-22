<?php

namespace Allen\Basic\Element\Trait;

trait Media
{
	use Element;
	public function MediaGet(): ?string
	{
		return $this->AttributeGet('media');
	}
	public function MediaSet(?string $media): self
	{
		if (!is_null($media)) {
			$this->AttributeAdd('media', $media);
		} else {
			$this->AttributeRemove('media');
		}
		return $this;
	}
	public function MediaSetArray(?array $media): self
	{
		if (!empty($media)) {
			return $this->MediaSet(implode(' ', $media));
		}
		return $this->MediaSet(null);
	}
	public function MediaSetScreen(
		?int $minWidth = null,
		?int $maxWidth = null,
	): self {
		$media = [];
		$media[] = 'screen';
		if (!is_null($minWidth) && $minWidth > 0 && (is_null($maxWidth) || $minWidth < $maxWidth)) $media[] = "and (min-width: {$minWidth}px)";
		if (!is_null($maxWidth) && $maxWidth > 0 && (is_null($minWidth) || $maxWidth > $minWidth)) $media[] = "and (max-width: {$maxWidth}px)";
		return $this->MediaSet(implode(' ', $media));
	}
}
