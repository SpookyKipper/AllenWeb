<?php

namespace Allen\Basic\Util;

use Allen\Web;
use Allen\Basic\Element\Button;
use Allen\Basic\Element\Button\ButtonLink;

class APP
{
	const USER_AGENT = [
		'AllenAPP',
	];
	const WEB = [
		'https://app-web.asallenshih.tw',
	];
	protected static null|bool|string $version = null;
	public static function Get(): bool|string
	{
		self::$version ??= self::_GetUA() ?? self::_GetWeb() ?? false;
		return self::$version;
	}
	public static function Is(): bool
	{
		return self::Get() !== false;
	}
	public static function IsWeb(): bool
	{
		return self::Get() === true;
	}
	public static function Version(): ?string
	{
		$version = self::Get();
		if ($version === true || $version === false) {
			return null;
		}
		return $version;
	}
	public static function InVersion(string $start = '0.0.0', ?string $end = null): bool
	{
		$version = self::Version();
		if ($version === null) {
			if (self::IsWeb()) {
				return true;
			}
			return false;
		}
		if ($end === null) {
			return version_compare($version, $start, '>=');
		}
		return version_compare($version, $start, '>=') && version_compare($version, $end, '<=');
	}
	public static function Open(?string $url = null): never
	{
		global $title;
		$title = Language::Output([
			'zh-Hant-TW' => '開啟' . Config::Get('util.app.name', 'AllenAPP'),
			'en' => 'Open ' . Config::Get('util.app.name', 'AllenAPP'),
		]);
		Web::Start();
?>
		<div>
			<h3>安裝AllenAPP</h3>
			<p>如未安裝AllenAPP，請點擊下方的按鈕，安裝AllenAPP。</p>
			<?= new ButtonLink(content: '下載AllenAPP', href: 'https://app.asallenshih.tw/') ?>
			<?= new Button(content: '其他裝置開啟網頁版', id: 'open-web') ?>
		</div>
		<script>
			const openWebButton = document.getElementById('open-web');
			const go = '<?= $url ?? '' ?>';
			openWebButton?.addEventListener('click', () => {
				window.location.replace(`https://app-go.asallenshih.tw/web${go === '' ? '' : `/${go}`}`);
			});
			window.addEventListener('load', () => {
				window.location.replace(`allenapp:/${go}`);
			});
		</script>
<?php
		Web::End();
		exit;
	}
	protected static function _GetUA(): ?string
	{
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			foreach (self::USER_AGENT as $user_agent) {
				if (str_starts_with($_SERVER['HTTP_USER_AGENT'], $user_agent . '/')) {
					// 利用 regex 取得版本號
					if (preg_match('/' . preg_quote($user_agent, '/') . '\/([0-9]+\.[0-9]+\.[0-9]+)/', $_SERVER['HTTP_USER_AGENT'], $matches)) {
						return $matches[1];
					}
				}
			}
		}
		return null;
	}
	protected static function _GetWeb(): ?bool
	{
		return self::_GetWebHeader('HTTP_ORIGIN') ?? self::_GetWebHeader('HTTP_REFERER');
	}
	protected static function _GetWebHeader(string $header_name): ?bool
	{
		if (isset($_SERVER[$header_name])) {
			foreach (self::WEB as $web) {
				if ($_SERVER[$header_name] === $web || str_starts_with($_SERVER[$header_name], $web . '/')) {
					return true;
				}
			}
		}
		return null;
	}
}
