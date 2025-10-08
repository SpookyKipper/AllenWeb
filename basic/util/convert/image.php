<?php

namespace Allen\Basic\Util\Convert;

use Allen\Basic\Util\Convert\Image\{OutputFormat, OutputMethod, ResizeMethod};
use GdImage;

/**
 * 圖片處理
 */
class Image
{
	public function __construct(
		protected GdImage $image
	) {}
	public function GetImage(): GdImage
	{
		return $this->image;
	}
	/**
	 * 取得圖片尺寸
	 * @return array{int, int} [寬度, 高度]
	 */
	public function GetSize(): array
	{
		if (false === $x = imagesx($this->image) || false === $y = imagesy($this->image)) {
			return [0, 0];
		}
		return [$x, $y];
	}
	public function Output(OutputFormat $format = OutputFormat::WebP, OutputMethod $method = OutputMethod::Direct, int $quality = -1, ?string $file = null)
	{
		return match ($method) {
			OutputMethod::Resource => self::_OutputResource($this->image, $format, $quality),
			OutputMethod::String => self::_OutputString($this->image, $format, $quality),
			OutputMethod::Direct => self::_OutputDirect($this->image, $format, $quality, $file),
			default => null,
		};
	}
	/**
	 * 縮放圖片
	 * @param int|null $width 目標寬度，null表示自動計算
	 * @param int|null $height 目標高度，null表示自動計算
	 * @param ResizeMethod $method 縮放方法
	 * @param bool $allow_larger 是否允許放大圖片
	 * @return $this
	 */
	public function Resize(
		?int $width,
		?int $height,
		ResizeMethod $method = ResizeMethod::FitIn,
		bool $allow_larger = false,
	): self {
		[$image_width, $image_height] = $this->GetSize();
		[$new_width, $new_height] = self::_ResizeCalcSize(
			image_width: $image_width,
			image_height: $image_height,
			width: $width,
			height: $height,
			method: $method,
			allow_larger: $allow_larger,
		);
		if ($new_width <= 0 || $new_height <= 0) return $this;
		$new_image = imagecreatetruecolor($new_width, $new_height);
		if ($new_image === false) return $this;
		if (imagesavealpha($new_image, true) === false) return $this;
		if (!imagecopyresampled(
			$new_image,
			$this->image,
			0,
			0,
			($method === ResizeMethod::Crop ? (int)max(round(($image_width - ($image_height * ($new_width / $new_height))) / 2, 1), 0) : 0),
			($method === ResizeMethod::Crop ? (int)max(round(($image_height - ($image_width * ($new_height / $new_width))) / 2, 1), 0) : 0),
			$new_width,
			$new_height,
			($method === ResizeMethod::Crop ? (int)min(round($image_width - ($image_height * ($new_width / $new_height)), 1), $image_width) : $image_width),
			($method === ResizeMethod::Crop ? (int)min(round($image_height - ($image_width * ($new_height / $new_width)), 1), $image_height) : $image_height),
		)) return $this;
		$this->image = $new_image;
		return $this;
	}
	/**
	 * 從字串建立圖片物件
	 * @param string $data 圖片二進位資料
	 * @return self|null 圖片物件，失敗回傳null
	 */
	public static function FromString(string $data): ?self
	{
		$mime = @finfo_buffer(finfo_open(\FILEINFO_MIME_TYPE), $data);
		if (in_array($mime, [
			'image/webp',
			'image/png',
		])) {
			$function = 'imagecreatefrom' . substr($mime, 6);
			if (function_exists($function)) {
				/**
				 * @var GdImage|resource|null $image
				 */
				$image = @$function('data://' . $mime . ';base64,' . base64_encode($data)) ?: null;
			}
			unset($function);
		}
		$image ??= @imagecreatefromstring($data) ?: null;
		if (is_null($image)) {
			return null;
		}
		return new self(
			image: $image,
		);
	}
	/**
	 * 計算縮放後的尺寸
	 * @param int $image_width 原始圖片寬度
	 * @param int $image_height 原始圖片高度
	 * @param int|null $width 目標寬度，null表示自動計算
	 * @param int|null $height 目標高度，null表示自動計算
	 * @param ResizeMethod $method 縮放方法
	 * @param bool $allow_larger 是否允許放大圖片
	 * @return array{int, int} [新寬度, 新高度]
	 */
	protected static function _ResizeCalcSize(
		int $image_width,
		int $image_height,
		?int $width,
		?int $height,
		ResizeMethod $method,
		bool $allow_larger = false,
	): array {
		if ($image_width <= 0 || $image_height <= 0) return [0, 0];
		else if ($width === null && $height === null) return [$image_width, $image_height];
		else if ($width === null) $width = (int)max(round($image_width * ($height / $image_height)), 1);
		else if ($height === null) $height = (int)max(round($image_height * ($width / $image_width)), 1);
		$result = match ($method) {
			ResizeMethod::Stretch => [$width, $height],
			ResizeMethod::Crop => [$width, $height],
			ResizeMethod::FitOut => [
				(int)max(round($image_width * max($width / $image_width, $height / $image_height)), 1),
				(int)max(round($image_height * max($width / $image_width, $height / $image_height)), 1),
			],
			ResizeMethod::FitIn => [
				(int)max(round($image_width * min($width / $image_width, $height / $image_height)), 1),
				(int)max(round($image_height * min($width / $image_width, $height / $image_height)), 1),
			],
		};
		if (!$allow_larger) {
			if ($result[0] > $image_width) {
				$result[1] = (int)max(round($image_height * ($result[0] / $image_width)), 1);
				$result[0] = $image_width;
			}
			if ($result[1] > $image_height) {
				$result[0] = (int)max(round($image_width * ($result[1] / $image_height)), 1);
				$result[1] = $image_height;
			}
		}
		$result[0] = min($result[0], 100000);
		$result[1] = min($result[1], 100000);
		return $result;
	}
	protected static function _OutputFunction(OutputFormat $format): ?string
	{
		$func = match ($format) {
			OutputFormat::JPEG => 'imagejpeg',
			OutputFormat::PNG => 'imagepng',
			OutputFormat::WebP => 'imagewebp',
			default => null,
		};
		if ($func === null || !function_exists($func)) {
			return null;
		}
		return $func;
	}
	protected static function _OutputResource(GdImage $image, OutputFormat $format, int $quality)
	{
		$output_function = self::_OutputFunction($format);
		if ($output_function === null) {
			return null;
		}
		$tmpfile = tmpfile();
		if ($tmpfile === false) {
			return null;
		}
		if (!@$output_function($image, $tmpfile, $quality) || !rewind($tmpfile)) {
			fclose($tmpfile);
			return null;
		}
		return $tmpfile;
	}
	protected static function _OutputString(GdImage $image, OutputFormat $format, int $quality)
	{
		$tmpfile = self::_OutputResource($image, $format, $quality);
		if ($tmpfile === null) {
			return null;
		}
		$data = stream_get_contents($tmpfile);
		fclose($tmpfile);
		if ($data === false) {
			return null;
		}
		return $data;
	}
	protected static function _OutputDirect(GdImage $image, OutputFormat $format, int $quality, ?string $file = null)
	{
		$tmpfile = self::_OutputResource($image, $format, $quality);
		if ($tmpfile === null) {
			return false;
		}
		@header('Content-Type: ' . match ($format) {
			OutputFormat::JPEG => 'image/jpeg',
			OutputFormat::PNG => 'image/png',
			OutputFormat::WebP => 'image/webp',
			default => 'application/octet-stream',
		});
		if (!is_null($file)) {
			@header('Content-Disposition: attachment; filename="' . basename($file) . '"');
		}
		return fpassthru($tmpfile);
	}
	protected static function _OutputFile(GdImage $image, OutputFormat $format, int $quality, ?string $file)
	{
		$output_function = self::_OutputFunction($format);
		if ($output_function === null || is_null($file)) {
			return false;
		}
		return @$output_function($image, $file, $quality);
	}
}
