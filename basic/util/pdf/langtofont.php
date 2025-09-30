<?php

namespace Allen\Basic\Util\Pdf;

use Mpdf\Language\LanguageToFont;

class LangToFont extends LanguageToFont
{
	public function __construct(protected Fonts $fonts) {}
	public function getLanguageOptions($llcc, $adobeCJK)
	{
		return $this->fonts->ToLangToFont($llcc, $adobeCJK) ?? parent::getLanguageOptions($llcc, $adobeCJK);
	}
}
