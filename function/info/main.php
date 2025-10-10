<?php

namespace Allen\Function;

use Allen\Basic\Element\Button\ButtonLink;
use Allen\Basic\Util\{Language, Request};
use Allen\Function\Info\InfoId;
use Allen\Web;

class Info
{
	/**
	 * 所有可用資訊。鍵為資訊ID，值為以語言代碼為鍵，標題為值的陣列
	 * @var array<string, array<string, string>>
	 */
	public readonly array $infos;
	public function __construct(
		public readonly ?array $list = null,
		public readonly string $request_base = 'https://cdn.asallenshih.tw/info',
		public readonly array $request_header = [],
		public readonly string $info_index = 'https://go.asallenshih.tw/info',
		public readonly string $info_id = 'https://go.asallenshih.tw/info/{id}',
		public readonly string $info_version = 'https://go.asallenshih.tw/info/{id}/v{version}',
		public readonly string $info_compare = 'https://go.asallenshih.tw/info/{id}/v{version}/c{compare}',
	) {
		$request = $this->Request('main');
		if ($request['code'] !== 200 || !is_array($request['response'])) {
			http_response_code(500);
			exit;
		}
		$this->infos = array_filter(
			array_map(
				fn($i) => array_filter(
					$i,
					fn($l) => in_array($l, array_keys(Language::LANGS)),
				),
				is_null($this->list)
					? $request['response']
					: array_diff_key(
						$request['response'],
						array_flip($this->list),
					),
			),
			fn($v) => !empty($v),
		);
	}
	/**
	 * @var InfoId[]
	 */
	private array $ids = [];
	/**
	 * 取得指定資訊
	 * @param string $id 資訊ID
	 */
	public function Id(string $id): InfoId
	{
		$cache = array_find($this->ids, fn($v) => $v->id === $id);
		if (!is_null($cache)) return $cache;
		$new = new InfoId($this, $id);
		$this->ids[] = $new;
		return $new;
	}
	/**
	 * 取得所有支援的語言代碼
	 * @return string[]
	 */
	public function Lang(): array
	{
		return array_values(array_unique(array_merge(...array_map(fn($v) => array_keys($v), $this->infos))));
	}
	/**
	 * 設定支援的語言
	 */
	public function LangSetSupport(): self
	{
		Language::SetSupport(...$this->Lang());
		return $this;
	}
	public function PathList(): array
	{
		$layer = [];
		foreach ($this->infos as $id => $langs) {
			$id_path = explode('/', $id);
			$current = &$layer;
			foreach ($id_path as $part) {
				if (!array_key_exists($part, $current)) $current[$part] = [];
				$current = &$current[$part];
			}
			$current = [
				true,
			];
		}
		return $layer;
	}
	/**
	 * 顯示網頁
	 */
	public function Web(): void
	{
		global $title;
		$this->LangSetSupport();
		$title ??= Language::Output([
			'en-US' => 'Related Information',
			'zh-Hant-TW' => '相關資訊',
		]);
		Web::Start();
?>
		<h2><?= Language::Output([
				'en-US' => 'Please refer to the information below',
				'zh-Hant-TW' => '請詳閱下列資訊',
			]) ?></h2>
		<?php
		$this->WebList($this->PathList());
		Web::End();
	}
	public function WebList(array $layer, array $path = []): void
	{
		foreach ($layer as $key => $value) {
			if (is_int($key) || !is_array($value)) continue;
			$path_string = implode('/', [...$path, $key]);
		?>
			<div class="list">
				<div class="list-title">
					<?= Language::Output($this->infos[$path_string] ?? [
						'en-US' => 'Category',
						'zh-Hant-TW' => '類別',
					]) ?>
				</div>
				<div class="list-content">
					<?php if (in_array(true, $value, true)) { ?>
						<?= ButtonLink::Create(
							content: Language::Output([
								'en-US' => 'View Details',
								'zh-Hant-TW' => '查看詳情',
							]),
							link: str_replace([
								'{id}',
							], [
								$path_string,
							], $this->info_id),
							lang: true,
						) ?>
					<?php } ?>
					<?php $this->WebList($value, [...$path, $key]); ?>
				</div>
			</div>
<?php
		}
	}
	public function Request(string $path)
	{
		return (new Request(
			url: $this->request_base . '/' . $path . '.json',
			header: [
				'Accept' => 'application/json',
				...$this->request_header,
			],
		))->GET();
	}
}
