<?php

namespace Allen\Basic\Util;

use Allen\Basic\Path;

class Cache
{
	protected ?array $data = null;
	public function __construct(
		protected string $id,
		protected int $expire = 0,
		protected ?string $path = null,
	) {
		$this->id = preg_replace('#(\.\./|\.\.\\\\|\.\.)#', '', $this->id);
		if ($this->expire < 0) {
			$this->expire = 0;
		}
		$this->path ??= Path::Cache();
	}
	protected function Read(): ?array
	{
		if ($this->data !== null) {
			return $this->data;
		} else if (!$this->Exist()) {
			return null;
		}
		try {
			$data = json_decode(file_get_contents($this->path . $this->id . '.json'), true);
			return is_array($data) ? $data : null;
		} catch (\Throwable $e) {
			return null;
		}
	}
	public function Exist(): bool
	{
		return is_file($this->path . $this->id . '.json');
	}
	public function IsValid(): ?bool
	{
		$data = $this->Read();
		if ($data === null) {
			return null;
		} else if ($this->expire === 0) {
			return true;
		} else if (isset($data['expire']) && is_int($data['expire'])) {
			return $data['expire'] >= time();
		}
		return false;
	}
	public function Get(bool $force = false): ?array
	{
		if ($this->IsValid() === null || !$force && !$this->IsValid()) {
			return null;
		}
		return $this->Read();
	}
	public function Set(array $data): bool
	{
		try {
			if (!is_dir(dirname($this->path . $this->id . '.json'))) {
				mkdir(dirname($this->path . $this->id . '.json'), 0755, true);
			}
			$this->data = $data;
			$this->data['expire'] = $this->expire === 0 ? 0 : time() + $this->expire;
			file_put_contents($this->path . $this->id . '.json', json_encode($this->data));
			return true;
		} catch (\Throwable $e) {
			return false;
		}
	}
	public function Delete(): bool
	{
		if (!$this->Exist()) {
			return false;
		}
		try {
			unlink($this->path . $this->id . '.json');
			return true;
		} catch (\Throwable $e) {
			return false;
		}
	}
	public function GetString(bool $force = false): ?string
	{
		$data = $this->Get(force: $force);
		if (isset($data['data']) && is_string($data['data'])) {
			return $data['data'];
		}
		return null;
	}
	public function SetString(string $data): bool
	{
		return $this->Set(['data' => $data]);
	}
}
