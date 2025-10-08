<?php

namespace Allen\Basic\Util\Convert\Image;

/**
 * 輸出格式
 */
enum OutputFormat: string
{
	case JPEG = 'jpeg';
	case PNG = 'png';
	case WebP = 'webp';
}
