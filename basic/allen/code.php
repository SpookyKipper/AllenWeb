<?php
/**
 * 輸出程式碼
 * @param array|string $code 程式碼
 * @param string|null $lang 程式語言
 * @return string HTML輸出
 */
function allen_code(array|string $code, ?string $lang = null): string
{
	if (is_array($code)) {
		$code = implode(PHP_EOL, $code);
	}
	$code = htmlspecialchars($code);
	$code = str_replace(' ', '&nbsp;', $code);
	return '<div class="flex"><pre class="max-width text left" style="overflow: auto;"><code' . (is_string($lang) ? ' class="language-' . $lang . '"' : '') . '>' . $code . '</code></pre></div>';
}
