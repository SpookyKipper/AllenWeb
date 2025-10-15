<?php

namespace Allen\Basic\Element\Custom;

use Allen\Basic\Element\{Div, Span};

class CarouselTitle extends Div
{
	protected int $now = 0;
	protected int $total = 0;
	protected string $status;
	public function __construct(
		string $content = '{status}',
		string $status = '{now}/{total}',
	) {
		parent::__construct(
			content: $content,
			class: [
				'carousel-title',
			],
		);
		$this->StatusSet($status);
	}
	public function NowGet(): Span
	{
		return new Span(
			content: strval($this->now),
			id: is_null($this->IdGet()) ? null : $this->IdGet() . '-now',
		);
	}
	public function NowSet(int $now): self
	{
		$this->now = $now;
		return $this;
	}
	public function TotalGet(): Span
	{
		return new Span(
			content: strval($this->total),
			id: is_null($this->IdGet()) ? null : $this->IdGet() . '-total',
		);
	}
	public function TotalSet(int $total): self
	{
		$this->total = $total;
		return $this;
	}
	public function StatusGet(): Span
	{
		return new Span(
			content: str_replace(
				[
					'{now}',
					'{total}',
				],
				[
					$this->NowGet()->Render(),
					$this->TotalGet()->Render(),
				],
				$this->status,
			),
			id: is_null($this->IdGet()) ? null : $this->IdGet() . '-status',
		);
	}
	public function StatusSet(string $status): self
	{
		$this->status = $status;
		return $this;
	}
	public function ContentGet(): string
	{
		return str_replace(
			'{status}',
			$this->StatusGet()->Render(),
			parent::ContentGet(),
		);
	}
}
