<?php

namespace Allen\Basic\Element;

use Allen\Basic\Element\Enum\Rel as EnumRel;
use Allen\Basic\Element\Trait\{Href, Media, Rel};

class Link extends Element
{
	use Href, Media, Rel;
	public function __construct(
		?array $attribute = null,
		?EnumRel $rel = null,
		?string $href = null,
		?string $media = null,
		?int $mediaScreenMinWidth = null,
		?int $mediaScreenMaxWidth = null,
	) {
		parent::__construct(
			tag: 'link',
			selfClose: true,
			attribute: $attribute,
		);
		if (!is_null($rel)) $this->RelSet($rel);
		if (!is_null($href)) $this->HrefSet($href);
		if (!is_null($media)) $this->MediaSet($media);
		if (!is_null($mediaScreenMinWidth) || !is_null($mediaScreenMaxWidth)) $this->MediaSetScreen(
			minWidth: $mediaScreenMinWidth,
			maxWidth: $mediaScreenMaxWidth,
		);
	}
}
