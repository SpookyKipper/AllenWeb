<?php

namespace Allen\Basic\Element\Link;

use Allen\Basic\Element\Link;
use Allen\Basic\Element\Enum\Rel;

class Stylesheet extends Link
{
	public function __construct(
		?array $attribute = null,
		?string $href = null,
		?string $media = null,
	) {
		parent::__construct(
			attribute: $attribute,
			rel: Rel::Stylesheet,
			href: $href,
		);
		if (!is_null($media)) $this->MediaSet($media);
	}
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
	public static function MediaScreen(
		?array $attribute = null,
		?string $href = null,
		?int $minWidth = null,
		?int $maxWidth = null,
	): self {
		$media = 'screen';
		if (!is_null($minWidth)) $media .= " and (min-width: {$minWidth}px)";
		if (!is_null($maxWidth)) $media .= " and (max-width: {$maxWidth}px)";
		return new self(
			attribute: $attribute,
			href: $href,
			media: $media,
		);
	}
}
