<?php

namespace Allen\Basic\Element\Span;

use Allen\Basic\Element\Span;

class Symbol extends Span
{
	public function __construct(
		string $content = 'question_mark',
		?array $attribute = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
	) {
		parent::__construct(
			content: $content,
			attribute: $attribute,
			id: $id,
			class: array_merge(
				[
					'material-symbols-outlined',
				],
				$class ?? [],
			),
			style: $style,
		);
	}
}
