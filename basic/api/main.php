<?php

namespace Allen\Basic;

use Allen\Basic\Util\{Json, Server};
use Throwable;

class API
{
	public static function _Execute(
		string $service,
		int $version,
		?string $namespace_prefix = 'Allen\\apis',
		bool $error_handler = true,
	): void {
		if ($error_handler) {
			self::_ErrorHandler();
		}
		if (str_contains($service, '.')) {
			self::Error(400, 'Invalid service name, do not include "." character.');
		} else if (str_contains($service, '\\')) {
			self::Error(400, 'Invalid service name, do not include "\\" character.');
		} else if (str_contains($service, '//')) {
			self::Error(400, 'Invalid service name, do not include "//" character.');
		} else if (str_starts_with($service, '/') || str_ends_with($service, '/')) {
			self::Error(400, 'Invalid service name, do not start or end with "/" character.');
		} else if ($version < 1) {
			self::Error(400, 'Invalid version number, please use a positive integer.');
		}
		$namespace = (is_string($namespace_prefix) ? $namespace_prefix . '\\' : '') . str_replace('/', '\\', $service) . '\\v' . $version;
		$class = $namespace . '\\main';
		if (!class_exists($class)) {
			self::Error(404, 'Service class does not exist, please check the service name and version number.');
		}
		$method = Server::GetMethod();
		$method_ucf = ucfirst(strtolower($method));
		if (!interface_exists('Allen\\Basic\\API\\Type\\' . $method_ucf)) {
			self::Error(405, 'Request method ' . $method_ucf . ' is not supported.');
		} else if (!is_subclass_of($class, 'Allen\\Basic\\API\\Type\\' . $method_ucf)) {
			self::Error(405, 'Service does not support ' . $method_ucf . ' requests.');
		}
		call_user_func($class . '::' . $method_ucf);
	}
	public static function _Run(
		?string $namespace_prefix = 'Allen\\apis',
	): void {
		$service = self::InputQuery('service', required: true);
		$version = self::InputQuery('version', required: true);
		if (filter_var($version, FILTER_VALIDATE_INT) === false) {
			self::Error(400, 'Invalid version number, please use a positive integer.');
		}
		self::_Execute(
			service: $service,
			version: intval($version),
			namespace_prefix: $namespace_prefix,
		);
	}
	public static function InputHeader(?string $header = null, bool $required = true): array|string|null
	{
		if (is_null($header)) {
			return Server::GetHeaders();
		}
		$content = Server::GetHeader($header);
		if (is_null($content) && $required) {
			self::Error(400, "標頭 '$header' 是必需的。");
		}
		return $content;
	}
	public static function InputQuery(?string $query = null, bool $required = true): array|string|null
	{
		if (is_null($query)) {
			return $_REQUEST;
		} else if (isset($_REQUEST[$query])) {
			return $_REQUEST[$query];
		} else if ($required) {
			self::Error(400, "參數 '$query' 是必需的。");
		}
		return null;
	}
	public static function InputData(bool $required = true, bool $json = true): mixed
	{
		$data = file_get_contents('php://input');
		if (empty($data)) {
			if ($required) {
				self::Error(400, '請求的資料格式不得為空。');
			}
			return null;
		} else if ($json) {
			if (!Json::Validate($data)) {
				if ($required) {
					self::Error(400, '請求的資料格式錯誤，應為 JSON 格式。');
				}
				return null;
			}
			return Json::Decode($data);
		}
		return $data;
	}
	public static function Output(mixed $data, ?int $last_modified = null): never
	{
		if (is_int($last_modified)) {
			header('Last-Modified: ' . date('r', $last_modified));
			$if_modified_since_header = self::InputHeader('If-Modified-Since', false);
			if (is_string($if_modified_since_header) && strtotime($if_modified_since_header) >= $last_modified) {
				http_response_code(304);
				exit;
			}
		}
		Json::Output($data);
	}
	public static function Error(int $code = 500, ?string $message = null, ?int $message_id = null): never
	{
		http_response_code($code);
		self::Output([
			'success' => false,
			'error' => $message ?? true,
			'code' => $message_id ?? $code,
		]);
	}
	public static function _ErrorHandler(): void
	{
		set_error_handler(function ($errno, $errstr, $errfile, $errline) {
			if (!(error_reporting() & $errno)) {
				return false;
			}
			$errtype = match ($errno) {
				E_ERROR => 'Fatal Error',
				E_WARNING => 'Warning',
				E_PARSE => 'Parse Error',
				E_NOTICE => 'Notice',
				E_DEPRECATED => 'Deprecated Notice',
				default => 'Unknown Error',
			};
			if ($errtype === 'Notice') {
				return true;
			}
			self::Error(500, "Server Error. Please try again later.\n$errtype $errstr ($errfile:$errline)");
		}, E_ALL);
		error_reporting(E_ALL);
		set_exception_handler(function (Throwable $exception) {
			self::Error(500, 'Server Error. Please try again later.' . PHP_EOL . $exception->getMessage() . ' (' . $exception->getFile() . ':' . $exception->getLine() . ')');
		});
	}
}
