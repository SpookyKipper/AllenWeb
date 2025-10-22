<?php

namespace Allen\Basic\Element\Trait;

use Allen\Basic\Element\Enum\ScriptType as EnumScriptType;

trait ScriptType
{
	public function TypeGet(): ?EnumScriptType
	{
		$type = $this->AttributeGet('type');
		return !is_null($type) ? EnumScriptType::tryFrom($type) : null;
	}
	public function TypeSet(?EnumScriptType $type): self
	{
		return $this->AttributeAdd('type', $type?->value);
	}
}
