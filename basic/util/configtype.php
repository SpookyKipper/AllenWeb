<?php

namespace Allen\Basic\Util;

enum ConfigType: string
{
	case Mixed = 'mixed';
	case String = 'string';
	case Int = 'integer';
	case Float = 'double';
	case Bool = 'boolean';
	case Array = 'array';
	case Object = 'object';
	case Null = 'NULL';
	case Resource = 'resource';
	case Unknown = 'unknown type';
}
