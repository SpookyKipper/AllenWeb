<?php

namespace Allen\Basic\Element\Iframe;

use Allen\Basic\Element\{Iframe, Enum\Allow};

class YouTube extends Iframe
{
	public function __construct(
		?array $attribute = null,
		?string $id = null,
		?array $class = null,
		?array $style = null,
		float $widthPercent = 100,
		int $aspectRatioX = 16,
		int $aspectRatioY = 9,
		?string $videoId = null,
		?string $playlistId = null,
		?string $userId = null,
		?array $playlist = null,
		bool $autoplay = false,
		bool $loop = false,
		bool $controls = true,
		bool $disablekb = false,
		bool $fullscreen = true,
		?int $start = null,
		?int $end = null,
	) {
		parent::__construct(
			attribute: array_merge(
				[
					'referrerpolicy' => 'strict-origin-when-cross-origin',
				],
				$attribute ?? [],
			),
			id: $id,
			class: $class,
			style: array_merge(
				[
					'aspect-ratio' => $aspectRatioX . '/' . $aspectRatioY,
					'width' => $widthPercent . '%',
					'max-width' => 'calc(100vh * ' . $aspectRatioX . ' / ' . $aspectRatioY . ')',
					'max-height' => '100vh',
				],
				$style ?? [],
			),
			allow: [
				Allow::Accelerometer,
				Allow::Autoplay,
				Allow::ClipboardWrite,
				Allow::EncryptedMedia,
				Allow::Gyroscope,
				Allow::PictureInPicture,
				Allow::WebShare,
			],
			allowFullscreen: $fullscreen,
			src: self::ToSrc(
				videoId: $videoId,
				playlistId: $playlistId,
				userId: $userId,
				playlist: $playlist,
				autoplay: $autoplay,
				loop: $loop,
				controls: $controls,
				disablekb: $disablekb,
				fullscreen: $fullscreen,
				start: $start,
				end: $end,
			),
		);
	}
	static public function ToSrc(
		?string $videoId = null,
		?string $playlistId = null,
		?string $userId = null,
		?array $playlist = null,
		bool $autoplay = false,
		bool $loop = false,
		bool $controls = true,
		bool $disablekb = false,
		bool $fullscreen = true,
		?int $start = null,
		?int $end = null,
	): ?string {
		$base = !is_null($videoId) || !is_null($playlistId) || !is_null($userId) ? 'https://www.youtube-nocookie.com/embed/' . ($videoId ?? '') : null;
		if (is_null($base)) return null;
		$params = [];
		if (!is_null($playlistId)) {
			$params['listType'] = 'playlist';
			$params['list'] = $playlistId;
		} else if (!is_null($userId)) {
			$params['listType'] = 'user_uploads';
			$params['list'] = $userId;
		} else if ($playlist) $params['playlist'] = implode(',', $playlist);
		if ($autoplay) $params['autoplay'] = '1';
		if ($loop) {
			$params['loop'] = '1';
			if (!isset($params['playlist']) && !is_null($videoId)) $params['playlist'] = $videoId;
		}
		if (!$controls) $params['controls'] = '0';
		if ($disablekb) $params['disablekb'] = '1';
		if (!$fullscreen) $params['fs'] = '0';
		if (!is_null($start)) $params['start'] = $start;
		if (!is_null($end)) $params['end'] = $end;
		return $base . (!empty($params) ? '?' . http_build_query($params) : '');
	}
	public function VideoIdSet(
		?string $videoId = null,
		?string $playlistId = null,
		?string $userId = null,
		?array $playlist = null,
		bool $autoplay = false,
		bool $loop = false,
		bool $controls = true,
		bool $disablekb = false,
		bool $fullscreen = true,
		?int $start = null,
		?int $end = null,
	): self
	{
		$this->SrcSet(self::ToSrc(
				videoId: $videoId,
				playlistId: $playlistId,
				userId: $userId,
				playlist: $playlist,
				autoplay: $autoplay,
				loop: $loop,
				controls: $controls,
				disablekb: $disablekb,
				fullscreen: $fullscreen,
				start: $start,
				end: $end,
			));
		return $this;
	}
}
