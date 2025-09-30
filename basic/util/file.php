<?php

namespace Allen\Basic\Util;

class File
{
	public function __construct()
	{
		@ob_end_clean();
		set_time_limit(0);
	}
	protected $file_resource = null;
	protected string $file_base = './data';
	public function FileBaseGet(): string
	{
		return $this->file_base;
	}
	public function FileBaseSet(string $base): self
	{
		if (is_dir($base)) {
			$this->file_base = $base;
		} else {
			http_response_code(404);
			die();
		}
		return $this;
	}
	protected bool $list = false;
	public function ListSet(bool $list): self
	{
		$this->list = $list;
		return $this;
	}
	protected bool $cache_control = true;
	public function CacheControlGet(): bool
	{
		return $this->cache_control;
	}
	public function CacheControlSet(bool $cache_control): self
	{
		$this->cache_control = $cache_control;
		return $this;
	}
	protected ?string $content_type = null;
	public function ContentTypeGet(): ?string
	{
		return $this->content_type;
	}
	public function ContentTypeSet(?string $type): self
	{
		if (empty($type)) {
			$this->content_type = null;
		} else {
			$this->content_type = $type;
		}
		return $this;
	}
	protected ?string $download_name = null;
	public function DownloadNameGet(): ?string
	{
		return $this->download_name;
	}
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
	protected bool $download = false;
	public function DownloadGet(): bool
	{
		return $this->download;
	}
	public function DownloadSet(bool $download): self
	{
		$this->download = $download;
		return $this;
	}
	protected ?int $size = null;
	public function SizeGet(): ?int
	{
		return $this->size;
	}
	public function SizeSet(?int $size): self
	{
		if ($size === null || $size < 0) {
			$this->size = null;
		} else {
			$this->size = $size;
		}
		return $this;
	}
	protected ?int $last_modified = null;
	public function FileSet(string $file): self
	{
		if ($this->file_resource !== null) {
			fclose($this->file_resource);
			$this->file_resource = null;
		}
		if (!is_file($file)) {
			http_response_code(404);
			die();
		}
		$last_modified = @filemtime($file);
		if ($last_modified !== false) {
			$this->last_modified = $last_modified;
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
		} else {
			$this->last_modified = null;
		}
		$fp = @fopen($file, 'rb');
		if ($fp === false) {
			http_response_code(500);
			die();
		}
		$this->file_resource = $fp;
		return $this;
	}
	protected function FileTmpCreate(): self
	{
		if (is_resource($this->file_resource)) {
			fclose($this->file_resource);
			$this->file_resource = null;
		}
		$resource = tmpfile();
		if ($resource === false) {
			http_response_code(500);
			die();
		}
		$this->file_resource = $resource;
		return $this;
	}
	protected ?int $image_quality = null;
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
	protected ?int $image_width = null;
	public function ImageWidthGet(): ?int
	{
		return $this->image_width;
	}
	public function ImageWidthSet(?int $width = null): self
	{
		$this->image_width = $width;
		return $this;
	}
	protected ?int $image_height = null;
	public function ImageHeightGet(): ?int
	{
		return $this->image_height;
	}
	public function ImageHeightSet(?int $height = null): self
	{
		$this->image_height = $height;
		return $this;
	}
	protected int $_image_size_min = 1;
	protected int $_image_size_max = 100000;
	protected function _ImageResize(\GdImage $image): \GdImage
	{
		// 處理寬度和高度的情況
		if ($this->ImageWidthGet() === null && $this->ImageHeightGet() === null) {
			return $image;
		} else if ($this->ImageHeightGet() === null) {
			$this->ImageHeightSet(ceil(imagesy($image) * $this->ImageWidthGet() / imagesx($image)));
		} else if ($this->ImageWidthGet() === null) {
			$this->ImageWidthSet(ceil(imagesx($image) * $this->ImageHeightGet() / imagesy($image)));
		}
		// 檢查最小和最大尺寸
		if ($this->ImageWidthGet() < $this->_image_size_min || $this->ImageHeightGet() < $this->_image_size_min) {
			return $image;
		} else if ($this->ImageWidthGet() > $this->_image_size_max) {
			$this->ImageHeightSet(ceil($this->ImageHeightGet() * $this->_image_size_max / $this->ImageWidthGet()));
			$this->ImageWidthSet($this->_image_size_max);
			return $this->_ImageResize($image);
		} else if ($this->ImageHeightGet() > $this->_image_size_max) {
			$this->ImageWidthSet(ceil($this->ImageWidthGet() * $this->_image_size_max / $this->ImageHeightGet()));
			$this->ImageHeightSet($this->_image_size_max);
			return $this->_ImageResize($image);
		} else if ($this->ImageWidthGet() > imagesx($image)) {
			$this->ImageHeightSet(ceil($this->ImageHeightGet() * imagesx($image) / $this->ImageWidthGet()));
			$this->ImageWidthSet(imagesx($image));
			return $this->_ImageResize($image);
		} else if ($this->ImageHeightGet() > imagesy($image)) {
			$this->ImageWidthSet(ceil($this->ImageWidthGet() * imagesy($image) / $this->ImageHeightGet()));
			$this->ImageHeightSet(imagesy($image));
			return $this->_ImageResize($image);
		}
		// 執行圖像縮放
		$new_image = @imagecreatetruecolor($this->ImageWidthGet(), $this->ImageHeightGet());
		if ($new_image === false) {
			return $image;
		}
		imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 255, 255, 255, 127));
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		if (!@imagecopyresampled($new_image, $image, 0, 0, 0, 0, $this->ImageWidthGet(), $this->ImageHeightGet(), imagesx($image), imagesy($image))) {
			imagedestroy($new_image);
			return $image;
		}
		imagedestroy($image);
		return $new_image;
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
		$base64_data = 'data://' . $mime . ';base64,' . base64_encode($data);
		if ($mime === 'image/webp') {
			$image = @imagecreatefromwebp($base64_data);
		} else if ($mime === 'image/png') {
			$image = @imagecreatefrompng($base64_data);
		} else {
			$image = @imagecreatefromstring($data);
		}
		unset($base64_data);
		if ($image === false) {
			return $this;
		}
		$image = $this->_ImageResize($image);
		$this->FileTmpCreate();
		imagewebp($image, $this->file_resource, ($this->ImageQualityGet() ?? 80));
		imagedestroy($image);
		$this->ContentTypeSet('image/webp');
		$this->SizeSet(ftell($this->file_resource));
		$download_name = $this->DownloadNameGet();
		if (is_string($download_name)) {
			$this->DownloadNameSet(pathinfo($download_name, PATHINFO_FILENAME) . '.webp');
		}
		return $this;
	}
	protected function Send_CacheControl(): self
	{
		if ($this->CacheControlGet()) {
			header('Cache-Control: public, max-age=31536000, must-revalidate');
		} else {
			header('Cache-Control: no-cache, no-store, must-revalidate');
		}
		$headers = array_change_key_case(getallheaders(), \CASE_LOWER);
		if (isset($headers['if-modified-since'])) {
			$last_modified = strtotime($headers['if-modified-since']);
			if ($this->last_modified !== null && $last_modified >= $this->last_modified) {
				http_response_code(304);
				die();
			}
		}
		return $this;
	}
	protected function Send_ContentType(): self
	{
		header('Content-Type: ' . ($this->ContentTypeGet() ?? 'application/octet-stream'));
		return $this;
	}
	protected function Send_Download(): self
	{
		header('Content-Disposition: ' . ($this->DownloadGet() ? 'attachment' : 'inline') . ($this->DownloadNameGet() ? '; filename="' . $this->DownloadNameGet() . '"' : ''));
		return $this;
	}
	protected ?int $partial_size = null;
	protected int $range = 0;
	protected function Send_Size(): self
	{
		if (empty($this->size)) {
			return $this;
		}
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
					die();
				}
			} else {
				http_response_code(416);
				die();
			}
			http_response_code(206);
			header('Content-Range: bytes ' . $this->range . '-' . ($this->range + $this->partial_size - 1) . '/' . $this->size);
		} else {
			$this->partial_size = $this->size;
		}
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . $this->partial_size);
		return $this;
	}
	protected int $max_read_size = 1048576;
	protected function Send_File(): self
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
				flush();
			}
			fclose($this->file_resource);
		} catch (\Throwable $e) {
			http_response_code(500);
			die();
		}
		return $this;
	}
	public function ReadFile(string $file): void
	{
		if (
			$file !== '' &&
			(
				str_starts_with('/', $file) ||
				str_contains('\\', $file) ||
				str_contains('./', $file) ||
				str_contains('\\', $file)
			)
		) {
			http_response_code(404);
			die();
		} else if (is_dir($this->file_base . ($file === '' ? '' : '/' . $file))) {
			if (!$this->list) {
				http_response_code(404);
				die();
			}
			$title = ($title ?? '') . '檔案檢視';
			$base = &$this->file_base;
			$files = array_map(function ($ctx) use ($base, $file) {
				return [
					'name' => $ctx,
					'type' => is_dir($base . ($file === '' ? '' : '/' . $file) . '/' . $ctx) ? 'folder' : (is_file($base . ($file === '' ? '' : '/' . $file) . '/' . $ctx) ? 'draft' : 'unknown_document'),
				];
			}, array_diff(scandir($base . ($file === '' ? '' : '/' . $file)), ['.', '..', 'Thumbs.db', '.DS_Store', 'index.allen', 'index.php']));
			uasort($files, function ($a, $b) {
				return strnatcmp($a['name'], $b['name']);
			});
			require_once __DIR__ . '/../web/start.php';
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
			require_once __DIR__ . '/../web/end.php';
			die();
		} else if (!is_file($this->file_base . '/' . $file)) {
			http_response_code(404);
			die();
		}
		$this->FileSet($this->file_base . '/' . $file);
		@$mime = mime_content_type($this->file_base . '/' . $file);
		if ($mime) {
			$this->ContentTypeSet($mime);
		}
		@$size = filesize($this->file_base . '/' . $file);
		if ($size) {
			$this->SizeSet($size);
		}
		if (empty($this->DownloadNameGet())) {
			$this->DownloadNameSet($file);
		}
		$this->Send_CacheControl()
			->ImageResize()
			->Send_ContentType()
			->Send_Download()
			->Send_Size()
			->Send_File();
	}
}
