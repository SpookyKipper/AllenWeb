<?php

namespace Allen\Basic\Element\Trait;

use Allen\Basic\Element\Enum\Allow as EnumAllow;

trait Allow
{
	use Element;
	public function AllowGet(bool $returnNull = true): ?array
	{
		return $this->_AttributeKeyToArray(
			key: 'allow',
			returnNull: $returnNull,
			semicolon: true,
		);
	}
	public function AllowSet(string|EnumAllow ...$allows): self
	{
		$allows = array_map(
			fn($allow) => $allow instanceof EnumAllow ? $allow->value : $allow,
			$allows,
		);
		return $this->_AttributeKeyFromArray(
			key: 'allow',
			value: $allows,
			semicolon: true,
		);
	}
	public function AllowAdd(string|EnumAllow ...$allows): self
	{
		$current = $this->AllowGet(
			returnNull: false,
		);
		$allows = array_map(
			fn($allow) => $allow instanceof EnumAllow ? $allow->value : $allow,
			$allows,
		);
		return $this->AllowSet(...array_unique(array_merge($current, $allows)));
	}
	public function AllowRemove(string|EnumAllow ...$allows): self
	{
		$current = $this->AllowGet(
			returnNull: false,
		);
		$allows = array_map(
			fn($allow) => $allow instanceof EnumAllow ? $allow->value : $allow,
			$allows,
		);
		return $this->AllowSet(...array_diff($current, $allows));
	}
}
