<?php

namespace Allen\Basic\Element\Enum;

enum Target: string
{
	const Self = '_self';
	const Blank = '_blank';
	const Parent = '_parent';
	const Top = '_top';
}
