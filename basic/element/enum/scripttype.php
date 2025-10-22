<?php

namespace Allen\Basic\Element\Enum;

enum ScriptType: string
{
	case Default = 'text/javascript';
	case Module = 'module';
}