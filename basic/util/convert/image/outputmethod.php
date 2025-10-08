<?php

namespace Allen\Basic\Util\Convert\Image;

/**
 * 輸出方法
 */
enum OutputMethod: string
{
/**
	 * 直接輸出
	 */
	case Direct = 'direct';
/**
	 * 輸出字串
	 */
	case String = 'string';
/**
	 * 輸出資源
	 */
	case Resource = 'resource';
/**
	 * 輸出檔案
	 */
	case File = 'file';
}
