<?php

namespace Allen\Basic\Element;

use Allen\Basic\Element\Trait\{Allow, AllowFullscreen, Src};

class Iframe extends Element
{
	use Allow, AllowFullscreen, Src;
	public function __construct(
		?array $attribute = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
		?array $allow = null,
		?bool $allowFullscreen = null,
		?string $src = null,
	) {
		parent::__construct(
			tag: 'iframe',
			attribute: $attribute,
			content: '您的瀏覽器未啟用Iframe功能',
			id: $id,
			class: $class,
			style: array_merge([
				'border' => 'none',
			], $style ?? []),
		);
		if (!is_null($allow)) $this->AllowAdd(...$allow);
		if (!is_null($allowFullscreen)) $this->AllowfullscreenSet($allowFullscreen);
		if (!is_null($src)) $this->SrcSet($src);
	}
}
