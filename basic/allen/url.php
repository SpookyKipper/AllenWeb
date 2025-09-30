<?php
require_once __DIR__ . '/../../main.php';

use Allen\Basic\Util\Language;
use Allen\Basic\Util\Uri;

/**
 * 網址按鈕
 * @param string $text 按鈕文字
 * @param string $url 網址
 * @param bool $new 是否開新分頁
 */
function allen_url(string $url, bool $lang = false): string
{
	$uri = Uri::Parse($url);
	if ($lang) {
		if (Language::Get() === 'zh-Hant-TW') {
			$uri = $uri->RemoveQuery('lang');
		} else {
			$uri = $uri->AddQuery('lang', Language::Get());
		}
	}
	return $uri->Get();
}
