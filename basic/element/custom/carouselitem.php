<?php

namespace Allen\Basic\Element\Custom;

use Allen\Basic\Element\Div;

class CarouselItem extends Div
{
	use CarouselItemTrait;
	public function __construct(
		?string $content = null,
		?string $background = null,
		?float $background_opacity = null,
	) {
		parent::__construct(
			content: $content,
			class: [
				'carousel-item',
			],
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
