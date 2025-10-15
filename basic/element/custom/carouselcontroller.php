<?php

namespace Allen\Basic\Element\Custom;

use Allen\Basic\Element\{Button, Div, Span\Symbol};

class CarouselController extends Div
{
	protected bool $pause_play;
	protected bool $next_prev;
	public function __construct(
		bool $pause_play = true,
		bool $next_prev = true,
	) {
		parent::__construct(
			class: [
				'carousel-controller',
			],
		);
		$this->pause_play = $pause_play;
		$this->next_prev = $next_prev;
	}
	public function ContentGet(): string
	{
		$output = '';
		if ($this->pause_play) {
			$output = (new Button(
				content: (new Symbol(
					content: 'play_arrow',
					id: is_null($this->IdGet()) ? null : $this->IdGet() . '-play-icon',
				))->Render(),
				id: is_null($this->IdGet()) ? null : $this->IdGet() . '-play',
			))->Render();
		}
		if ($this->next_prev) {
			$output = (new Button(
				content: (new Symbol(
					content: 'arrow_back',
					id: is_null($this->IdGet()) ? null : $this->IdGet() . '-prev-icon',
				))->Render(),
				id: is_null($this->IdGet()) ? null : $this->IdGet() . '-prev',
			))->Render() . $output . (new Button(
				content: (new Symbol(
					content: 'arrow_forward',
					id: is_null($this->IdGet()) ? null : $this->IdGet() . '-next-icon',
				))->Render(),
				id: is_null($this->IdGet()) ? null : $this->IdGet() . '-next',
			))->Render();
		}
		return $output;
	}
}
