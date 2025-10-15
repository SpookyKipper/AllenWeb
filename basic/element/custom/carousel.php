<?php

namespace Allen\Basic\Element\Custom;

use Allen\Basic\Element\Div;

class Carousel extends Div
{
	use CarouselItemTrait;
	protected CarouselTitle $title;
	protected CarouselController $controller;
	protected int $interval;
	protected array $item = [];
	public function __construct(
		string $id,
		array $item = [],
		?CarouselTitle $title = null,
		?CarouselController $controller = null,
		int $interval = 5000,
		?string $background = null,
		?float $background_opacity = null,
		?array $class = null,
		?array $style = null,
	) {
		parent::__construct(
			id: $id,
			class: array_merge(
				[
					'carousel',
				],
				$class ?? [],
			),
			style: $style,
		);
		$this
			->ItemSet(...array_filter($item, fn($v) => $v instanceof CarouselItem || $v instanceof CarouselItemA))
			->TitleSet($title)
			->ControllerSet($controller)
			->IntervalSet($interval)
			->BackgroundSet($background)
			->BackgroundOpacitySet($background_opacity);
	}
	/**
	 * @return (CarouselItem|CarouselItemA)[]
	 */
	public function ItemGet(): array
	{
		return $this->item;
	}
	public function ItemSet(CarouselItem|CarouselItemA ...$item): self
	{
		$this->item = $item;
		return $this;
	}
	public function ItemAdd(CarouselItem|CarouselItemA ...$item): self
	{
		$this->ItemSet(...array_merge($this->ItemGet(), $item));
		return $this;
	}
	public function TitleGet(): CarouselTitle
	{
		return $this->title
			->NowSet(count($this->item) > 0 ? 1 : 0)
			->TotalSet(count($this->item))
			->IdSet(is_null($this->IdGet()) ? null : $this->IdGet() . '-title');
	}
	public function TitleSet(?CarouselTitle $title = null): self
	{
		$this->title = $title ?? new CarouselTitle();
		return $this;
	}
	public function ControllerGet(): CarouselController
	{
		return $this->controller->IdSet(is_null($this->IdGet()) ? null : $this->IdGet() . '-controller');
	}
	public function ControllerSet(?CarouselController $controller = null): self
	{
		$this->controller = $controller ?? new CarouselController();
		return $this;
	}
	public function IntervalGet(): int
	{
		return $this->interval;
	}
	public function IntervalSet(int $interval): self
	{
		$this->interval = $interval;
		return $this;
	}
	public function ContentGet(): string
	{
		if (!is_null($this->BackgroundGet())) array_map(function ($i) {
			if (is_null($i->BackgroundGet())) $i->BackgroundSet($this->BackgroundGet());
		}, $this->ItemGet());
		if (!is_null($this->BackgroundOpacityGet())) array_map(function ($i) {
			if (is_null($i->BackgroundOpacityGet())) $i->BackgroundOpacitySet($this->BackgroundOpacityGet());
		}, $this->ItemGet());
		return ($this->TitleGet()->Render()) . (new Div(
			id: is_null($this->IdGet()) ? null : $this->IdGet() . '-main',
			class: [
				'carousel-main',
			],
			style: [
				'--carousel-n' => '1',
			]
		))->ContentSet(...$this->item)->Render() . ($this->ControllerGet()->Render() ?? '') . (!is_null($this->IdGet()) ? '<script type="module">import c from "https://cdn.asallenshih.tw/js/function/Carousel.js";c("' . $this->IdGet() . '", ' . $this->IntervalGet() . ');</script>' : '');
	}
}
