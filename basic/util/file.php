<?php

namespace Allen\Basic\Util;

use Allen\Web;
use Allen\Basic\Util\Convert\{Image, Image\OutputFormat, Image\OutputMethod};

/**
 * 檔案處理
 */
class File
{
	/**
	 * 檔案資源
	 * @var resource|null
	 */
	protected $file_resource = null;
	/**
	 * 檔案根目錄
	 */
	protected ?string $file_base = null;
	/**
	 * 檔案大小
	 */
	protected ?int $size = null;
	/**
	 * 圖片品質(0-100)
	 */
	protected ?int $image_quality = null;
	/**
	 * @param resource|null $file_resource 檔案資源
	 * @param string $file_base 檔案根目錄
	 * @param bool $list 是否允許顯示目錄列表
	 * @param bool|null $cache_control 是否啟用快取控制，預設null為Private
	 * @param string|null 設定內容類型，預設null為自動偵測或application/octet-stream
	 * @param string|null $download_name 下載檔案名稱，預設null為不設定
	 * @param bool $download 是否以瀏覽器下載檔案方式提供檔案，預設為 false
	 * @param int|null $last_modified 檔案最後修改時間，預設null為不設定
	 */
	public function __construct(
		$file_resource = null,
		$file_base = './data',
		protected bool $list = false,
		protected ?bool $cache_control = null,
		protected ?string $content_type = null,
		protected ?string $download_name = null,
		protected bool $download = false,
		protected ?int $last_modified = null,
		?int $image_quality = null,
		protected ?int $image_width = null,
		protected ?int $image_height = null,
	) {
		@ob_end_clean();
		set_time_limit(0);
		$this
			->FileResourceSet($file_resource)
			->FileBaseSet($file_base)
			->ImageQualitySet($image_quality);
	}
	/**
	 * 取得檔案資源
	 * @return resource|null
	 */
	public function FileResourceGet()
	{
		return $this->file_resource;
	}
	/**
	 * 設定檔案資源
	 * @param resource|null $resource 檔案資源
	 */
	public function FileResourceSet($resource): self
	{
		if (is_resource($this->file_resource)) {
			fclose($this->file_resource);
			$this->file_resource = null;
		}
		$this->file_resource = (is_resource($resource)) ? $resource : null;
		$this->SizeSet($this->file_resource ? @fstat($this->file_resource)['size'] ?? null : null);
		return $this;
	}
	/**
	 * 取得檔案根目錄
	 */
	public function FileBaseGet(): ?string
	{
		return $this->file_base;
	}
	/**
	 * 設定檔案根目錄
	 * @param string|null $base 檔案根目錄
	 */
	public function FileBaseSet(?string $base): self
	{
		$this->file_base = (is_dir($base)) ? $base : null;
		return $this;
	}
	/**
	 * 取得是否允許顯示目錄列表
	 */
	public function ListGet(): bool
	{
		return $this->list;
	}
	/**
	 * 設定是否允許顯示目錄列表
	 */
	public function ListSet(bool $list): self
	{
		$this->list = $list;
		return $this;
	}
	/**
	 * 取得是否啟用快取控制
	 * @return bool|null
	 */
	public function CacheControlGet(): ?bool
	{
		return $this->cache_control;
	}
	/**
	 * 設定是否啟用快取控制
	 * @param bool|null $cache_control 是否啟用快取控制，null為Private
	 */
	public function CacheControlSet(?bool $cache_control): self
	{
		$this->cache_control = $cache_control;
		return $this;
	}
	/**
	 * 取得內容類型
	 * @return string|null
	 */
	public function ContentTypeGet(): ?string
	{
		return $this->content_type;
	}
	/**
	 * 設定內容類型
	 * @param string|null $type 內容類型，null為application/octet-stream
	 */
	public function ContentTypeSet(?string $type): self
	{
		if (empty($type)) {
			$this->content_type = null;
		} else {
			$this->content_type = $type;
		}
		return $this;
	}
	/**
	 * 取得下載檔案名稱
	 * @return string|null 下載檔案名稱，null表示不設定
	 */
	public function DownloadNameGet(): ?string
	{
		return $this->download_name;
	}
	/**
	 * 設定下載檔案名稱
	 * @param string|null $name 下載檔案名稱，null表示不設定
	 */
	public function DownloadNameSet(?string $name): self
	{
		if (empty($name)) {
			$this->download_name = null;
		} else {
			$path_array = explode('/', str_replace('\\', '/', $name));
			$set_name = array_pop($path_array);
			if (empty($set_name)) {
				$this->download_name = null;
			} else {
				$this->download_name = $set_name;
			}
		}
		return $this;
	}
	/**
	 * 取得是否以瀏覽器下載檔案方式提供檔案
	 * @return bool 是否以瀏覽器下載檔案方式提供檔案
	 */
	public function DownloadGet(): bool
	{
		return $this->download;
	}
	/**
	 * 設定是否以瀏覽器下載檔案方式提供檔案
	 * @param bool $download 是否以瀏覽器下載檔案方式提供檔案
	 */
	public function DownloadSet(bool $download): self
	{
		$this->download = $download;
		return $this;
	}
	/**
	 * 取得檔案最後修改時間
	 * @return int|null 檔案最後修改時間，null表示不設定
	 */
	public function LastModifiedGet(): ?int
	{
		return $this->last_modified;
	}
	/**
	 * 設定檔案最後修改時間
	 * @param int|null $time 檔案最後修改時間，null表示不設定
	 */
	public function LastModifiedSet(?int $time): self
	{
		$this->last_modified = (is_int($time) && $time >= 0) ? $time : null;
		return $this;
	}
	/**
	 * 取得檔案大小
	 * @return int|null 檔案大小，null表示不設定
	 */
	public function SizeGet(): ?int
	{
		return $this->size;
	}
	/**
	 * 設定檔案大小
	 * @param int|null $size 檔案大小，null表示不設定
	 */
	public function SizeSet(?int $size): self
	{
		$this->size = (is_int($size) && $size >= 0) ? $size : null;
		return $this;
	}
	/**
	 * 設定檔案
	 */
	public function FileFullSet(string $file): self
	{
		if (is_file($file)) $this
			->LastModifiedSet(@filemtime($file) ?: null)
			->ContentTypeSet(@mime_content_type($file) ?: null)
			->FileResourceSet(@fopen($file, 'rb') ?: null);
		return $this;
	}
	/**
	 * 設定檔案路徑
	 */
	public function FileSet(?string $file): self
	{
		if (!empty($file) && !empty($file_base = $this->FileBaseGet())) {
			$full_file = rtrim(($file_base ?? ''), '/') . '/' . ltrim($file, '/');
			if (is_file($full_file)) {
				$this->FileFullSet($full_file);
			}
		}
		return $this;
	}
	public function ImageQualityGet(): ?int
	{
		return $this->image_quality;
	}
	public function ImageQualitySet(?int $quality = null): self
	{
		if ($quality !== null && $quality < 0 && $quality > 100) {
			$this->image_quality = $quality;
		}
		return $this;
	}
	public function ImageWidthGet(): ?int
	{
		return $this->image_width;
	}
	public function ImageWidthSet(?int $width = null): self
	{
		$this->image_width = $width;
		return $this;
	}
	public function ImageHeightGet(): ?int
	{
		return $this->image_height;
	}
	public function ImageHeightSet(?int $height = null): self
	{
		$this->image_height = $height;
		return $this;
	}
	public function ImageResize(): self
	{
		if (($this->ImageWidthGet() === null && $this->ImageHeightGet() === null && $this->ImageQualityGet() === null) || !is_resource($this->file_resource)) {
			return $this;
		}
		$data = @stream_get_contents($this->file_resource);
		if ($data === false) {
			return $this;
		}
		$mime = @finfo_buffer(finfo_open(\FILEINFO_MIME_TYPE), $data);
		if ($mime === false || !str_starts_with($mime, 'image/')) {
			return $this;
		}
		$image = Image::FromString($data);
		if ($image === null) {
			return $this;
		}
		$this
			->FileResourceSet($image->Resize(
				width: $this->ImageWidthGet(),
				height: $this->ImageHeightGet(),
			)->Output(
				format: OutputFormat::WebP,
				method: OutputMethod::Resource,
				quality: $this->ImageQualityGet() ?? -1
			))
			->ContentTypeSet('image/webp');
		$download_name = $this->DownloadNameGet();
		if (is_string($download_name)) {
			$this->DownloadNameSet(pathinfo($download_name, PATHINFO_FILENAME) . '.webp');
		}
		return $this;
	}
	protected function SendHeader_ContentType(): self
	{
		header('Content-Type: ' . ($this->ContentTypeGet() ?? 'application/octet-stream'));
		return $this;
	}
	protected function SendHeader_Download(): self
	{
		header('Content-Disposition: ' . ($this->DownloadGet() ? 'attachment' : 'inline') . ($this->DownloadNameGet() ? '; filename="' . $this->DownloadNameGet() . '"' : ''));
		return $this;
	}
	protected function SendHeader_CacheControl(): self
	{
		$cc = $this->CacheControlGet();
		if ($cc === true) header('Cache-Control: public, max-age=31536000, must-revalidate');
		else if ($cc === false) header('Cache-Control: no-cache, no-store, must-revalidate');
		else header('Cache-Control: private, max-age=86400, must-revalidate');
		return $this;
	}
	protected function SendHeader_LastModified(): self
	{
		if ($this->last_modified !== null) {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $this->last_modified) . ' GMT');
			$if_modified_since = Server::GetHeader('If-Modified-Since');
			if ($if_modified_since !== null && strtotime($if_modified_since) >= $this->last_modified) {
				http_response_code(304);
				exit;
			}
		}
		return $this;
	}
	protected function SendHeader(): self
	{
		return $this
			->SendHeader_ContentType()
			->SendHeader_CacheControl()
			->SendHeader_Download()
			->SendHeader_LastModified();
	}
	protected ?int $partial_size = null;
	protected int $range = 0;
	protected function SendSize(): self
	{
		if (!empty($this->size)) {
			if (isset($_SERVER['HTTP_RANGE'])) {
				$range = $_SERVER['HTTP_RANGE'];
				if (preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches)) {
					$this->range = intval($matches[1]);
					if (isset($matches[2])) {
						$this->partial_size = intval($matches[2]) - $this->range + 1;
					} else {
						$this->partial_size = $this->size - $this->range;
					}
					if ($this->partial_size < 0 || $this->range >= $this->size) {
						http_response_code(416);
						exit;
					}
				} else {
					http_response_code(416);
					exit;
				}
				http_response_code(206);
				header('Content-Range: bytes ' . $this->range . '-' . ($this->range + $this->partial_size - 1) . '/' . $this->size);
			}
			$this->partial_size ??= $this->size;
			header('Accept-Ranges: bytes');
			header('Content-Length: ' . $this->partial_size);
		}
		return $this;
	}
	protected int $max_read_size = 1048576;
	protected function SendFile(): self
	{
		try {
			if (!is_resource($this->file_resource)) {
				http_response_code(500);
				die();
			}
			fseek($this->file_resource, $this->range);
			while (!feof($this->file_resource) && $this->partial_size > 0) {
				echo fread($this->file_resource, min($this->max_read_size, $this->partial_size));
				$this->partial_size -= $this->max_read_size;
				if ($this->partial_size < 0) {
					break;
				}
				@ob_flush();
				@flush();
			}
			fclose($this->file_resource);
		} catch (\Throwable $e) {
			http_response_code(500);
			die();
		}
		return $this;
	}
	public function Send(): self
	{
		return $this
			->SendHeader()
			->SendSize()
			->SendFile();
	}
	public function ReadFile(string $file): void
	{
		if ((
			$file !== '' &&
			(
				str_starts_with('/', $file) ||
				str_contains('\\', $file) ||
				str_contains('./', $file) ||
				str_contains('\\', $file)
			)
		) || $this->FileBaseGet() === null) {
			http_response_code(404);
			die();
		} else if (is_dir($this->FileBaseGet() . ($file === '' ? '' : '/' . $file))) {
			if (!$this->list) {
				http_response_code(404);
				die();
			}
			global $title;
			$title = ($title ?? '') . '檔案檢視';
			$base = &$this->FileBaseGet();
			$files = array_map(function ($ctx) use ($base, $file) {
				return [
					'name' => $ctx,
					'type' => is_dir($base . ($file === '' ? '' : '/' . $file) . '/' . $ctx) ? 'folder' : (is_file($base . ($file === '' ? '' : '/' . $file) . '/' . $ctx) ? 'draft' : 'unknown_document'),
				];
			}, array_diff(scandir($base . ($file === '' ? '' : '/' . $file)), ['.', '..', 'Thumbs.db', '.DS_Store', 'index.allen', 'index.php']));
			uasort($files, function ($a, $b) {
				return strnatcmp($a['name'], $b['name']);
			});
			Web::Start();
?>
			<div class="flex">
				<div class="text left">
					<?php foreach ($files as $ctx) { ?>
						<p>
							<span class="allen material-symbols-outlined"><?= $ctx['type'] ?></span>
							<a class="allen1" href="?id=<?= urlencode(($file === '' ? '' : $file . '/') . $ctx['name']) ?>"><?= $ctx['name'] ?></a>
						</p>
					<?php } ?>
				</div>
			</div>
<?php
			Web::End();
			die();
		} else if (!is_file($this->FileBaseGet() . '/' . $file)) {
			http_response_code(404);
			die();
		}
		$this->FileSet($file)->Send();
	}
}
