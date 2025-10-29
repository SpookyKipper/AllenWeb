<?php

namespace Allen\Basic\Util;

class Header
{
	public static function Json(?string $text, ?string $charset = 'utf-8', bool $etag = true, ?int $last_modified = null): void
	{
		self::SetContentType('application/json', charset: $charset);
		self::SetLastModified($last_modified);
		if ($etag) self::ETag($text);
		if (is_string($text)) self::ContentLength($text);
	}
	public static function ContentLength(string $text): void
	{
		self::SetContentLength(strlen($text));
	}
	public static function ContentDisposition(bool $attachment = false, ?string $filename = null): void
	{
		self::SetContentDisposition($attachment ? 'attachment' : 'inline', filename: $filename);
	}
	public static function ETag(string $data): void
	{
		self::SetETag('"' . sha1($data) . '"');
	}
	public static function Set(string $name, string $value, bool $replace = true): void
	{
		@header($name . ': ' . $value, $replace);
	}
	public static function SetContentType(string $type, ?string $charset = 'utf-8'): void
	{
		self::Set('Content-Type', $type . (is_string($charset) ? '; charset=' . $charset : ''));
	}
	public static function SetContentLength(int $length): void
	{
		self::Set('Content-Length', strval($length));
	}
	public static function SetContentDisposition(string $disposition, ?string $filename = null): void
	{
		self::Set('Content-Disposition', $disposition . (is_string($filename) ? '; filename="' . $filename . '"' : ''));
	}
	public static function SetETag(string $etag): void
	{
		self::Set('ETag', $etag);
		$if_none_match = Server::GetHeader('If-None-Match');
		if (is_string($if_none_match) && $if_none_match === $etag) {
			http_response_code(304);
			exit;
		}
	}
	public static function SetLastModified(?int $timestamp = null): void
	{
		if (is_null($timestamp)) return;
		self::Set('Last-Modified', gmdate('r', $timestamp));
		$if_modified_since = Server::GetHeader('If-Modified-Since');
		if (is_string($if_modified_since) && strtotime($if_modified_since) >= $timestamp) {
			http_response_code(304);
			exit;
		}
	}
	public static function SetAcceptRanges(string $unit = 'none'): void
	{
		self::Set('Accept-Ranges', $unit);
	}
}
