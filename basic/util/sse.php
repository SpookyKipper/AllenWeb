<?php

namespace Allen\Basic\Util;

/**
 * Server-Sent Events (SSE)
 */
class SSE
{
	public function __construct(
		public string $data,
		public ?string $event = null,
	) {}
	public static function Init(): void
	{
		ini_set('output_buffering', 'off');
		ini_set('zlib.output_compression', 'off');
		header('X-Accel-Buffering: no');
		header('Cache-Control: no-cache, no-transform');
		header('Connection: keep-alive');
		header('Content-Type: text/event-stream; charset=utf-8');
		while (ob_get_level() > 0) {
			ob_end_flush();
		}
	}
	public static function Encode(self $sse): string
	{
		$output = '';
		if (!is_null($sse->event)) {
			$output .= 'event: ' . $sse->event . \PHP_EOL;
		}
		$output .= 'data: ' . $sse->data . \PHP_EOL;
		$output .= \PHP_EOL;
		return $output;
	}
	public static function Send(string $content, bool $aborted_exit = false): void
	{
		if ($aborted_exit && connection_aborted()) {
			exit;
		}
		echo $content;
		@ob_flush();
		@flush();
	}
	public static function SendSSE(self $sse, bool $aborted_exit = false): void
	{
		self::Send(
			content: self::Encode($sse),
			aborted_exit: $aborted_exit,
		);
	}
	public static function SendData(string $data, ?string $event = null, bool $aborted_exit = false): void
	{
		self::SendSSE(
			sse: new self(
				data: $data,
				event: $event,
			),
			aborted_exit: $aborted_exit,
		);
	}
	public static function SendDataJson(mixed $data, ?string $event = null, bool $aborted_exit = false): void
	{
		self::SendData(
			data: Json::Encode($data),
			event: $event,
			aborted_exit: $aborted_exit,
		);
	}
}
