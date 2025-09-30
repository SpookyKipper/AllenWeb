<?php

namespace Allen\Basic\Util;

require_once __DIR__ . '/../../main.php';

use Allen\Basic\Util\Pdf\Fonts;
use Allen\Basic\Util\Pdf\LangToFont;
use Allen\Basic\Util\Pdf\PdfOutput;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Output\Destination;

class Pdf
{
	protected Mpdf $pdf;
	public function __construct()
	{
		$fonts = new Fonts();
		$defaultConfig = (new ConfigVariables())->getDefaults();
		$defaultFontConfig = (new FontVariables())->getDefaults();
		$this->pdf = new Mpdf([
			'default_font' => 'notosanstc',
			'fontDir' => array_merge($defaultConfig['fontDir'], [__DIR__ . '/pdf/font']),
			'fontdata' => $fonts->ToFontdata($defaultFontConfig['fontdata']),
			'languageToFont' => new LangToFont($fonts),
		]);
		$this->pdf->autoScriptToLang = true;
		$this->pdf->autoLangToFont = true;
	}
	public function AddPage()
	{
		return $this->pdf->AddPage();
	}
	public function AddBookmark(string $text, int $level, int $y)
	{
		return $this->pdf->Bookmark($text, $level, $y);
	}
	public function SetHeader(string $header)
	{
		return $this->pdf->SetHtmlHeader($header);
	}
	public function SetFooter(string $footer)
	{
		return $this->pdf->SetHtmlFooter($footer);
	}
	public function AddContent(string $content)
	{
		return $this->pdf->WriteHTML($content);
	}
	public function AddTableOfContentsPage()
	{
		return $this->pdf->TOCpagebreak();
	}
	public function AddTableOfContents(string $txt, int $level = 0, int $toc_id = 0)
	{
		return $this->pdf->TOC_Entry($txt, $level, $toc_id);
	}
	public function GetOutput(PdfOutput $type = PdfOutput::ToString, ?string $name = null, ?string $file_path = null, bool $return_file = false, bool $browser_attachment = false): mixed
	{
		try {
			$string = @$this->pdf->Output(dest: Destination::STRING_RETURN);
			if (!is_string($string) || empty($string)) {
				return false;
			}
			switch ($type) {
				case PdfOutput::ToString:
					return $string;
				case PdfOutput::ToFile:
					$fp = false;
					if (!is_null($name) && !is_null($file_path)) {
						if (!is_dir($file_path)) {
							$mkdir_result = @mkdir($file_path, 0755, true);
							if (!$mkdir_result) {
								return false;
							}
						}
						$fp = @fopen($file_path . '/' . $name . '.pdf', 'wb+');
					}
					if ($fp === false) {
						if (!$return_file) {
							return false;
						}
						$fp = @tmpfile();
						if ($fp === false) {
							return false;
						}
					}
					$write_result = @fwrite($fp, $string);
					if ($write_result === false) {
						@fclose($fp);
						return false;
					} else if ($return_file) {
						$fseek = @fseek($fp, 0);
						if ($fseek !== 0) {
							@fclose($fp);
							return false;
						}
						return $fp;
					}
					@fclose($fp);
					return true;
				case PdfOutput::ToBrowser:
					header('Content-Type: application/pdf');
					header('Content-Disposition: ' . ($browser_attachment ? 'attachment' : 'inline') . (empty($name) ? '' : '; filename="' . $name . '.pdf"'));
					header('Content-Length: ' . strlen($string));
					echo $string;
					return true;
				default:
					return false;
			}
		} catch (\Throwable $e) {
			return false;
		}
	}
	// 樣式
	static public function StyleTextAlign($content): array
	{
		return ['text-align' => $content];
	}
	static public function StyleTextAlignCenter(): array
	{
		return self::StyleTextAlign('center');
	}
	static public function StyleTextAlignRight(): array
	{
		return self::StyleTextAlign('right');
	}
	// 屬性類型
	static public function Style(array ...$styles): string
	{
		$latest_styles = [];
		foreach ($styles as $style) {
			if (is_array($style)) {
				foreach ($style as $key => $value) {
					$latest_styles[$key] = $value;
				}
			}
		}
		$result_styles = [];
		foreach ($latest_styles as $key => $value) {
			if (is_string($value) && !empty($value)) {
				$result_styles[] = htmlspecialchars($key) . ': ' . htmlspecialchars($value) . ';';
			}
		}
		return 'style="' . implode(' ', $result_styles) . '"';
	}
	// 屬性
	static public function Attribute(string ...$attribute): string
	{
		return empty($attribute) ? '' : ' ' . implode(' ', $attribute);
	}
	// 版面元素
	static public function Text(string $text): string
	{
		return htmlspecialchars($text);
	}
	static public function Span(string $content, array $attribute = []): string
	{
		return '<span' . self::Attribute(...$attribute) . '>' . $content . '</span>';
	}
	static public function Paragraph(string $content, array $attribute = []): string
	{
		return '<p' . self::Attribute(...$attribute) . '>' . $content . '</p>';
	}
	static public function Div(string $content, array $attribute = []): string
	{
		return '<div' . self::Attribute(...$attribute) . '>' . $content . '</div>';
	}
	static public function Table(?string $thead = null, array $tbody = [], ?string $tfoot = null): string
	{
		if ($thead === null && empty($tbody) && $tfoot === null) {
			return '';
		}
		$html = '<table style="width: 100%; border-collapse: collapse;">';
		if ($thead !== null) {
			$html .= '<thead><tr>' . $thead . '</tr></thead>';
		}
		if (!empty($tbody)) {
			$html .= '<tbody>';
			foreach ($tbody as $row) {
				$html .= '<tr>' . $row . '</tr>';
			}
			$html .= '</tbody>';
		}
		if ($tfoot !== null) {
			$html .= '<tfoot><tr>' . $tfoot . '</tr></tfoot>';
		}
		return $html . '</table>';
	}
	static public function TableRow(array $cells, bool $header = false): string
	{
		$html = '';
		foreach ($cells as $cell) {
			$preg = '/^<t(?:h|d)([^>]*)>(.*?)<\/(?:h|d)>$/';
			if (preg_match($preg, $cell, $matches) !== 0) {
				$html .= $cell;
			} else {
				$tag = 't' . ($header ? 'h' : 'd');
				$html .= '<' . $tag . ' style="border: 1px solid black; padding: 4px;">' . $cell . '</' . $tag . '>';
			}
		}
		return $html;
	}
	static public function TableData(string $content, array $attribute = []): string
	{
		return '<td' . self::Attribute(...$attribute) . '>' . $content . '</td>';
	}
	// 建立預設 PDF
	static public function GeneratePdfDefault(): Pdf
	{
		$pdf = new self();
		$pdf->SetFooter(self::Div('{PAGENO} / {nbpg}', attribute: [self::Style(self::StyleTextAlignCenter())]));
		return $pdf;
	}
}
