<?php

namespace Allen\Basic\Util\Convert\Image;

/**
 * 縮放方法
 */
enum ResizeMethod: string
{
/**
	 * 變形
	 */
	case Stretch = 'stretch';
/**
	 * 裁切
	 */
	case Crop = 'crop';
/**
	 * 保持比例縮放至範圍外
	 */
	case FitOut = 'fit_out';
/**
	 * 保持比例縮放至範圍內
	 */
	case FitIn = 'fit_in';
}
