<?php

namespace Allen\Basic\Util\Language;

class ZhHantTW
{
	public const Digit = [
		'零' => '零',
		'一' => '壹',
		'二' => '貳',
		'三' => '參',
		'四' => '肆',
		'五' => '伍',
		'六' => '陸',
		'七' => '柒',
		'八' => '捌',
		'九' => '玖',
	];
	public const Unit = [
		'十' => '拾',
		'百' => '佰',
		'千' => '仟',
		'萬' => '萬',
		'億' => '億',
		'兆' => '兆',
		'京' => '京',
	];
	/**
	 * 將數字轉換為中文數字
	 * @param int $number 要轉換的數字
	 * @param bool $big 是否使用大寫中文數字
	 * @return string 轉換後的中文數字
	 */
	public static function NumberText(int $number, bool $big = false): string
	{
		$digitMap = $big ? array_values(self::Digit) : array_keys(self::Digit);
		$unitMap = $big ? array_values(self::Unit) : array_keys(self::Unit);
		$output = '';
		if ($number === 0) return $digitMap[0];
		if ($number < 0) {
			$output = '負';
			$number = abs($number);
		}
		$numberStr = strval($number);
		$length = strlen($numberStr);
		$zero = false;
		foreach (str_split($numberStr) as $i => $char) {
			$num = intval($char);
			$pos = $length - $i - 1;
			$unitIdx = $pos % 4;
			$section = intdiv($pos, 4);
			if ($num === 0) $zero = true;
			else {
				// 補0
				if ($zero) {
					$output .= $digitMap[0];
					$zero = false;
				}
				// 加上數字
				$output .= $digitMap[$num];
				// 加上小單位
				if ($unitIdx > 0) $output .= $unitMap[$unitIdx - 1];
			}
			// 加上大單位
			if ($unitIdx === 0 && $pos > 0 && isset($unitMap[2 + $section])) $output .= $unitMap[2 + $section];
		}
		if (str_starts_with($output, $digitMap[1] . $unitMap[0]) && !$big) $output = mb_substr($output, 1);
		return $output;
	}
}
