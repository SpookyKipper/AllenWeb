<?php
require_once __DIR__ . '/url.php';
/**
 * 網址按鈕
 * @param string $text 按鈕文字
 * @param string $url 網址
 * @param bool $new 是否開新分頁
 */
function allen_button(string $text, string $url, bool $new = false, bool $lang = false): string
{
	return '<a href="' . allen_url($url, $lang) . '"' . ($new ? 'target="_blank"' : '') . '><button type="button">' . $text . '</button></a>';
}
