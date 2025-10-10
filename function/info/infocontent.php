<?php

namespace Allen\Function\Info;

use Allen\Basic\Util\Language;
use Allen\Basic\Util\Pdf;
use Allen\Web;

class InfoContent
{
	/**
	 * 用於比較的內容
	 */
	public ?self $compare = null;
	public readonly array $content;
	public function __construct(
		public readonly InfoDetail $detail,
		public readonly string $lang,
	) {
		if (!in_array($this->lang, $this->detail->lang)) {
			http_response_code(404);
			exit;
		}
		$request = $this->detail->Request($this->lang);
		if ($request['code'] !== 200 || !is_array($request['response'])) {
			http_response_code(500);
			exit;
		}
		$this->content = $request['response'];
	}
	public function Html(): string
	{
		return self::GenContentHtml(
			lang: $this->lang,
			data: $this->content,
			compare_data: is_null($this->compare) ? null : $this->compare->content,
		);
	}
	public function Web(): void
	{
		global $title;
		$title ??= Language::Output($this->detail->id->info->infos[$this->detail->id->id] ?? [], lang: $this->lang);
		Web::Start();
?>
		<div class="flex">
			<div class="max-width text left">
				<?php $this->detail->id->VersionInfoWeb($this->lang, $this->detail->version); ?>
				<?php $this->detail->id->CompareInfoWeb($this->lang, $this->detail->version, $this->compare); ?>
				<?php $this->detail->DateTimeWeb($this->lang) ?>
				<?= $this->Html(); ?>
			</div>
		</div>
		<?php $this->detail->id->VersionListWeb($this->lang, $this->detail->version, is_null($this->compare) ? null : $this->compare->detail->version); ?>
<?php
		Web::End();
	}
	public function Pdf(): void
	{
		$pdf = Pdf::GeneratePdfDefault();
		$name = Language::Output($this->detail->id->info->infos[$this->detail->id->id], lang: $this->lang);
		$pdf->SetHeader($name);
		$pdf->AddPage();
		$pdf->AddContent('<h1>' . $name . '</h1>');
		$pdf->AddContent($this->detail->DateTimePdf($this->lang));
		$pdf->AddPage();
		$pdf->AddContent($this->Html());
		$pdf->GetOutput(
			type: Pdf\PdfOutput::ToBrowser,
			name: $name . '.pdf',
		);
	}
	protected static function GenContentHtml(string $lang, array $data, ?array $compare_data = null, string $layer_previous = ''): string
	{
		$layer_text = Language::Output([
			'en-US' => '-',
			'zh-Hant-TW' => '之',
		], lang: $lang);
		$delete_text = Language::Output([
			'en-US' => '(Deleted)',
			'zh-Hant-TW' => '廢',
		], lang: $lang);
		$layer_count = mb_substr_count($layer_previous, $layer_text);
		$layer_header = min(6, $layer_count + 2);
		$count_current = 1;
		$new_key = [];
		$delete_key = [];
		$output = '';
		if (is_array($compare_data)) {
			$new_key = array_diff(array_keys($data), array_keys($compare_data));
			$delete_key = array_diff(array_keys($compare_data), array_keys($data));
			foreach ($delete_key as $key) $data[$key] = $compare_data[$key];
		}
		foreach ($data as $key => $value) {
			$count_current_text = Language::Output([
				'en-US' => strval($count_current),
				'zh-Hant-TW' => Language\ZhHantTW::NumberText($count_current),
			]);
			if (!is_numeric($key)) {
				if (in_array($key, $new_key, true) || in_array($key, $delete_key, true)) $output .= '<div style="background-color: #' . (in_array($key, $new_key) ? '78fa64' : 'eb3223') . '30;">';
				$output .= '<h' . $layer_header . '>' . $layer_previous . (in_array($key, $delete_key, true) ? $delete_text : $count_current_text) . '.' . htmlspecialchars($key) . '</h' . $layer_header . '><div style="margin-left: 16px;">';
				if (!in_array($key, $delete_key)) $count_current++;
			}
			if (is_string($value)) {
				if (!empty($value) && is_array($compare_data)) $value = self::GenCompare($input_compare[$key] ?? '', in_array($key, $delete_key) ? '' : $value);
				$output .= '<p>' . self::GenCovertHtml($value) . '</p>';
			} else if (is_array($value)) {
				$output .= self::GenContentHtml(
					lang: $lang,
					data: in_array($key, $delete_key) ? [] : $value,
					compare_data: $input_compare[$key] ?? null,
					layer_previous: $layer_previous . (!in_array($key, $delete_key) ? $count_current_text : $delete_text) . $layer_text
				);
			}
			if (!is_numeric($key)) {
				$output .= '</div>';
				if (in_array($key, $new_key) || in_array($key, $delete_key)) $output .= '</div>';
			}
		}
		return $output;
	}
	protected static function GenCovertHtml(string $text): string
	{
		return preg_replace([
			'/\[btn:(.*?)\]\((.*?)\)/',
			'/\[info:(.*?)\]\((.*?)\)/',
			'/\!\[(.*?)\]\((.*?)\)/',
			'/\[(.*?)\]\((.*?)\)/',
			'/\n/',
		], [
			'<a href="$2" target="_blank"><button type="button">$1</button></a>',
			'<a href="https://go.asallenshih.tw/info/$2" target="_blank" class="allen1">$1</a>',
			'<img src="$2" alt="$1" class="max-width">',
			'<a href="$2" target="_blank" class="allen1">$1</a>',
			'<br>',
		], $text);
	}
	/**
	 * 產生比較後的 HTML 內容
	 * @param string $oldText 舊內容
	 * @param string $newText 新內容
	 * @return string 比較後的 HTML 內容
	 */
	protected static function GenCompare(string $oldText = '', string $newText = ''): string
	{
		$oldChars = preg_split('//u', $oldText, -1, PREG_SPLIT_NO_EMPTY);
		$newChars = preg_split('//u', $newText, -1, PREG_SPLIT_NO_EMPTY);
		$diff = self::GenCompareCalculate($oldChars, $newChars);
		$output = '';
		$now_type = 0;
		foreach ($diff as $item) {
			if ($item['type'] !== $now_type) {
				$output .= '</span>';
				$now_type = $item['type'];
				if ($now_type === 1) $output .= '<span style="background-color: #78fa6430;">';
				else if ($now_type === -1) $output .= '<span style="background-color: #eb322330;">';
			}
			$output .= $item['text'][0];
		}
		if ($now_type !== 0) $output .= '</span>';
		return $output;
	}
	protected static function GenCompareCalculate(array $oldChars, array $newChars): array
	{
		$matrix = [];
		$oLen = count($oldChars);
		$nLen = count($newChars);
		for ($i = 0; $i <= $oLen; $i++) $matrix[$i] = array_fill(0, $nLen + 1, 0);
		for ($i = 1; $i <= $oLen; $i++) for ($j = 1; $j <= $nLen; $j++) {
			if ($oldChars[$i - 1] === $newChars[$j - 1]) $matrix[$i][$j] = $matrix[$i - 1][$j - 1] + 1;
			else $matrix[$i][$j] = max($matrix[$i - 1][$j], $matrix[$i][$j - 1]);
		}
		$result = [];
		$i = $oLen;
		$j = $nLen;
		while ($i > 0 && $j > 0) {
			if ($oldChars[$i - 1] === $newChars[$j - 1]) {
				array_unshift($result, ['type' => 0, 'text' => [$oldChars[$i - 1]]]);
				$i--;
				$j--;
			} else if ($matrix[$i - 1][$j] >= $matrix[$i][$j - 1]) {
				array_unshift($result, ['type' => -1, 'text' => [$oldChars[$i - 1]]]);
				$i--;
			} else {
				array_unshift($result, ['type' => 1, 'text' => [$newChars[$j - 1]]]);
				$j--;
			}
		}
		while ($i > 0) {
			array_unshift($result, ['type' => 'removed', 'text' => [$oldChars[$i - 1]]]);
			$i--;
		}
		while ($j > 0) {
			array_unshift($result, ['type' => 'added', 'text' => [$newChars[$j - 1]]]);
			$j--;
		}
		return $result;
	}
}
