<?php

namespace Allen\Function\Info;

use Allen\Basic\Element\Button\ButtonLink;
use Allen\Basic\Util\Language;
use Allen\Function\Info;

class InfoId
{
	/**
	 * 目前版本
	 */
	public readonly ?int $version;
	/**
	 * 所有版本
	 * @var int[]
	 */
	public readonly array $versions;
	public function __construct(
		public readonly Info $info,
		public readonly string $id,
	) {
		$request = $this->Request('main');
		if ($request['code'] !== 200 || !is_array($request['response'])) {
			http_response_code(500);
			exit;
		}
		$this->versions = isset($request['response']['versions']) && is_array($request['response']['versions'])
			? array_values(array_filter($request['response']['versions'], fn($v) => is_int($v)))
			: [];
		if (empty($this->versions)) {
			http_response_code(500);
			exit;
		}
		$this->version = isset($request['response']['version']) && is_int($request['response']['version'])
			? $request['response']['version']
			: $this->versions[array_key_last($this->versions)];
	}
	/**
	 * @var InfoDetail[]
	 */
	private array $detail = [];
	public function Detail(?int $version = null): InfoDetail
	{
		$version ??= $this->version ?? $this->versions[array_key_last($this->versions)];
		$cache = array_find($this->detail, fn($v) => $v->version === $version);
		if (!is_null($cache)) return $cache;
		$new = new InfoDetail($this, $version);
		$this->detail[] = $new;
		return $new;
	}
	public function VersionInfoWeb(?string $lang = null, int $current_version): void
	{
		if ($current_version === $this->version) return;
?>
		<h2 class="error">
			<a href="<?= str_replace([
							'{id}',
							'{version}',
						], [
							$this->id,
							$this->version,
						], $this->info->info_version) ?>" class="error">
				<?= Language::Output([
					'en-US' => 'View Current Effective Version',
					'zh-Hant-TW' => '查看目前生效版本',
				], lang: $lang) ?>
			</a>
		</h2>
	<?php
	}
	public function CompareInfoWeb(?string $lang = null, int $current_version, ?InfoContent $compare): void
	{
		if (is_null($compare)) return;
	?>
		<h2>
			<a href="<?= str_replace([
							'{id}',
							'{version}',
						], [
							$this->id,
							$current_version,
						], $this->info->info_version) ?>" class="ok">
				<?= Language::Output([
					'en-US' => 'Exit Comparison Mode',
					'zh-Hant-TW' => '離開比較模式',
				], lang: $lang) ?>
			</a>
		</h2>
		<?= $this->CompareInfoExplan($lang); ?>
		<div style="background-color: #eb322330;">
			<?php $compare->detail->DateTimeWeb($lang); ?>
		</div>
	<?php
	}
	public function VersionListWeb(?string $lang = null, int $current_version, ?int $compare_version): void
	{
	?>
		<div class="card">
			<div class="flex">
				<p class="text left">
					<?php $this->VersionListWebVersion($lang, $current_version, $compare_version); ?><br>
					<?php $this->VersionListWebSelect($lang, $current_version, $compare_version, false); ?>
					<?php $this->VersionListWebSelect($lang, $current_version, $compare_version, true); ?>
				</p>
			</div>
			<p><?php $this->VersionListWebOther($lang); ?></p>
		</div>
	<?php
	}
	public function VersionText(?string $lang = null, int $version): string
	{
		return Language::Output([
			'zh-Hant-TW' => "第{$version}版",
			'en-US' => "Version {$version}",
		], lang: $lang);
	}
	protected function CompareInfoExplan(?string $lang = null): string
	{
		return '<div class="flex left"><h3 class="allen">' . Language::Output([
			'en-US' => 'Comparison Explanation:',
			'zh-Hant-TW' => '比較說明：',
		]) . '</h3><p><span style="background-color: #78fa6430;">' . Language::Output([
			'en-US' => 'Newly added',
			'zh-Hant-TW' => '新增',
		], lang: $lang) . '</span> <span>' . Language::Output([
			'en-US' => 'Unchanged',
			'zh-Hant-TW' => '未變動',
		], lang: $lang) . '</span> <span style="background-color: #eb322330;">' . Language::Output([
			'en-US' => 'Deleted',
			'zh-Hant-TW' => '刪除',
		], lang: $lang) . '</span></p></div>';
	}
	protected function VersionListWebVersion(?string $lang = null, int $current_version, ?int $compare_version): void
	{
		echo $this->VersionText($lang, $current_version);
		if ($current_version !== $this->version) {
			echo '(' . ($current_version > $this->version ? Language::Output([
				'zh-Hant-TW' => '未來版本',
				'en-US' => 'Future Version',
			], lang: $lang) : Language::Output([
				'zh-Hant-TW' => '過去版本',
				'en-US' => 'Past Version',
			], lang: $lang)) . ')';
		}
		if (!is_null($compare_version) && $compare_version !== $current_version) {
			$compare_version_text = $this->VersionText($lang, $compare_version);
			echo ' ' . Language::Output([
				'zh-Hant-TW' => "與 {$compare_version_text} 比較",
				'en-US' => "Compare to {$compare_version_text}",
			], lang: $lang);
		}
	}
	protected function VersionListWebSelect(?string $lang = null, int $current_version, ?int $compare_version, bool $compare): void
	{
		echo $compare ? Language::Output([
			'zh-Hant-TW' => '比較版本',
			'en-US' => 'Compare to',
		], lang: $lang) : Language::Output([
			'zh-Hant-TW' => '歷史版本',
			'en-US' => 'Version List',
		], lang: $lang);
	?>
		<select onchange="location.href=`${this.value}`;" class="max-width">
			<?php
			foreach ($this->versions as $version) {
				if ($compare && $version > $current_version) {
					continue;
				}
			?>
				<option value="<?= str_replace(
									['{id}', '{version}', '{compare}'],
									[$this->id, $compare ? $current_version : $version, $compare ? $version : ''],
									$compare ? $this->info->info_compare : $this->info->info_version
								) ?>" <?= ($version === ($compare ? $compare_version : $current_version) ? 'selected' : '') ?>>
					<?= Language::Output([
						'zh-Hant-TW' => "第{$version}版",
						'en-US' => "Version {$version}",
					], lang: $lang) ?>
					<?= (!$compare && $version !== $current_version ? '(' . ($version > $this->version ? Language::Output([
						'zh-Hant-TW' => '未來',
						'en-US' => 'Future',
					], lang: $lang) : Language::Output([
						'zh-Hant-TW' => '過去',
						'en-US' => 'Past',
					], lang: $lang)) . ')' : '') ?>
				</option>
			<?php
			}
			?>
		</select>
<?php
	}
	protected function VersionListWebOther(?string $lang = null): void
	{
		echo ButtonLink::Create(
			content: Language::Output([
				'zh-Hant-TW' => '查看其他資訊',
				'en-US' => 'View Other Information',
			], lang: $lang),
			link: $this->info->info_index,
		)->Render();
	}
	public function Request(string $path)
	{
		return $this->info->Request($this->id . '/' . $path);
	}
}
