<?php

namespace Allen\Basic\Util\Pdf;

class Fonts
{
	public array $fonts;
	public function __construct(
		Font ...$fonts
	) {
		$this->fonts = array_merge($fonts, [
			new Font(
				name: 'notosanstc',
				regular_file: 'NotoSansTC-Regular.ttf',
				bold_file: 'NotoSansTC-Bold.ttf',
				lang: 'zh',
				script: 'Hant',
				country: 'TW',
			),
			new Font(
				name: 'notosans',
				regular_file: 'NotoSans-Regular.ttf',
				bold_file: 'NotoSans-Bold.ttf',
				italic_file: 'NotoSans-Italic.ttf',
				bold_italic_file: 'NotoSans-BoldItalic.ttf',
			),
		]);
	}
	public function ToFontdata(array $defaultFontdata = []): array
	{
		$fontdata = $defaultFontdata;
		foreach ($this->fonts as $font) {
			if (($font instanceof Font) === false) {
				continue;
			}
			$fontdata[$font->name] = [
				'R' => $font->regular_file,
				'B' => $font->bold_file,
				'I' => $font->italic_file,
				'BI' => $font->bold_italic_file,
			];
		}
		return $fontdata;
	}
	public function ToLangToFont($llcc, $adobeCJK): ?array
	{
		$tags = explode('-', $llcc);
		$lang = strtolower($tags[0]);
		$country = null;
		$script = null;
		if (!empty($tags[1])) {
			if (strlen($tags[1]) === 4) {
				$script = strtolower($tags[1]);
			} else {
				$country = strtolower($tags[1]);
			}
		}
		if (!empty($tags[2])) {
			$country = strtolower($tags[2]);
		}
		$fonts_lang = array_filter($this->fonts, function ($font) use ($lang) {
			return ($font->lang === $lang);
		});
		$fonts_country = array_filter($fonts_lang, function ($font) use ($country) {
			return ($font->country === $country);
		});
		$fonts_country_script = array_filter($fonts_country, function ($font) use ($script) {
			return ($font->script === $script);
		});
		if (count($fonts_country_script) > 0) {
			return [false, $fonts_country_script[array_key_first($fonts_country_script)]->name];
		}
		if (count($fonts_country) > 0) {
			return [false, $fonts_country[array_key_first($fonts_country)]->name];
		}
		$fonts_script = array_filter($fonts_lang, function ($font) use ($script) {
			return ($font->script === $script);
		});
		if (count($fonts_script) > 0) {
			return [false, $fonts_script[array_key_first($fonts_script)]->name];
		}
		if (count($fonts_lang) > 0) {
			return [false, $fonts_lang[array_key_first($fonts_lang)]->name];
		}
		return null;
	}
}
