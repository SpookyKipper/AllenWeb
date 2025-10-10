<?php

namespace Allen\Function\Info;

use Allen\Basic\Element\Div;
use Allen\Basic\Util\Language;

class InfoDetail
{
	/**
	 * 發佈年份
	 */
	public readonly int $year;
	/**
	 * 發佈月份
	 */
	public readonly int $month;
	/**
	 * 發佈日
	 */
	public readonly int $day;
	/**
	 * 發布小時
	 */
	public readonly ?int $hour;
	/**
	 * 發布分鐘
	 */
	public readonly ?int $minute;
	/**
	 * 發布秒
	 */
	public readonly ?int $second;
	/**
	 * 可用語言
	 * @var string[]
	 */
	public readonly array $lang;
	public function __construct(
		public readonly InfoId $id,
		public readonly int $version,
	) {
		if (!in_array($this->version, $this->id->versions)) {
			http_response_code(404);
			exit;
		}
		$request = $this->Request('main');
		if (
			$request['code'] !== 200
			|| !is_array($request['response'])
			|| !isset($request['response']['date'])
			|| !is_array($request['response']['date'])
			|| count($request['response']['date']) !== 3
			|| in_array(false, array_map(fn($v) => is_int($v) && $v >= 0, $request['response']['date']), true)
			|| (
				isset($request['response']['time'])
				&& (
					!is_array($request['response']['time'])
					|| count($request['response']['time']) !== 3
					|| in_array(false, array_map(fn($v) => is_int($v) && $v >= 0, $request['response']['time']), true)
				)
			)
			|| !isset($request['response']['lang'])
			|| !is_array($request['response']['lang'])
		) {
			http_response_code(500);
			exit;
		}
		[$this->year, $this->month, $this->day] = $request['response']['date'];
		if (isset($request['response']['time'])) [$this->hour, $this->minute, $this->second] = $request['response']['time'];
		else [$this->hour, $this->minute, $this->second] = [null, null, null];
		$this->lang = array_values(array_filter($request['response']['lang'], fn($v) => array_key_exists($v, Language::LANGS)));
		if (empty($this->lang)) {
			http_response_code(500);
			exit;
		}
		usort($this->lang, function ($a, $b) {
			$index_a = array_search($a, Language::LANGS);
			$index_b = array_search($b, Language::LANGS);
			return $index_a <=> $index_b;
		});
	}
	/**
	 * @var InfoContent[]
	 */
	private array $content = [];
	public function Content(?string $lang = null): InfoContent
	{
		$lang ??= Language::GetSelect($this->lang);
		$cache = array_find($this->content, fn($v) => $v->lang === $lang);
		if (!is_null($cache)) return $cache;
		$new = new InfoContent($this, $lang);
		$this->content[] = $new;
		return $new;
	}
	public function LangSetSupport(): self
	{
		Language::SetSupport(...$this->lang);
		return $this;
	}
	public function DateTimeWeb(?string $lang = null): void
	{
		$date = $this->DateTime($lang);
		echo new Div(
			content: '<h2 class="allen">' . Language::Output([
				'en-US' => 'Last Modified: ',
				'zh-Hant-TW' => '最終修訂時間：',
			], lang: $lang) . '</h2><h3 class="allen1">' . $date . '</h3>',
			class: [
				'flex',
				'left',
			],
		);
	}
	public function DateTimePdf(?string $lang = null): string
	{
		return '<h2>' . Language::Output([
			'en-US' => 'Last Modified: ',
			'zh-Hant-TW' => '最終修訂時間：',
		], lang: $lang) . $this->DateTime($lang) . '</h2>';
	}
	protected function DateTime(?string $lang = null): string
	{
		$year = $this->year + Language::YearOffset($lang);
		$month = str_pad(strval($this->month), 2, '0', \STR_PAD_LEFT);
		$day = str_pad(strval($this->day), 2, '0', \STR_PAD_LEFT);
		$output = Language::Output([
			'en-US' => "{$year}-{$month}-{$day}",
			'zh-Hant-TW' => "{$year}年{$month}月{$day}日",
		], lang: $lang);
		if (!is_null($this->hour) && !is_null($this->minute) && !is_null($this->second)) {
			$hour = str_pad(strval($this->hour), 2, '0', \STR_PAD_LEFT);
			$minute = str_pad(strval($this->minute), 2, '0', \STR_PAD_LEFT);
			$second = str_pad(strval($this->second), 2, '0', \STR_PAD_LEFT);
			$output .= Language::Output([
				'en-US' => " {$hour}:{$minute}:{$second}",
				'zh-Hant-TW' => "{$hour}時{$minute}分{$second}秒",
			], lang: $lang);
		}
		return $output;
	}
	public function Request(string $path)
	{
		return $this->id->Request($this->version . '/' . $path);
	}
}
