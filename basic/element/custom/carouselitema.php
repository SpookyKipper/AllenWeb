<?php

namespace Allen\Basic\Element\Custom;

use Allen\Basic\Element\{A, Div, Enum\Target};

class CarouselItemA extends A
{
	use CarouselItemTrait;
	public function __construct(
		?string $content = null,
		?string $background = null,
		?float $background_opacity = null,
		?string $href = null,
		string|Target|null $target = null
	) {
		parent::__construct(
			content: $content,
			class: [
				'carousel-item-a',
			],
			href: $href,
			target: $target,
		);
		$this
			->BackgroundSet($background)
			->BackgroundOpacitySet($background_opacity);
	}
	public function ContentGet(): string
	{
		$output = parent::ContentGet();
		if ($this->BackgroundGet()) {
			$output .= (new Div(
				class: [
					'carousel-bg',
				],
				style: [
					'--carousel-bg' => "url('{$this->BackgroundGet()}')",
				]
			))->Render();
		}
		return $output;
	}
}
