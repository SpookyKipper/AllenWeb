<?php

namespace Allen\Basic\Util\Db\Session;

use Exception;
use SessionHandlerInterface;
use Allen\Basic\Util\Db\MySQL as DbMySQL;

class MySQL implements SessionHandlerInterface
{
	public function __construct(
		private DbMySQL $db,
	) {}
	public function open(string $savePath, string $sessionName): bool
	{
		return true;
	}
	public function close(): bool
	{
		return true;
	}
	public function read(string $id): string|false
	{
		try {
			$stmt = $this->db->prepare('SELECT `data` FROM `session` WHERE `id` = ? LIMIT 1');
			$stmt->execute([$id]);
			$row = $stmt->fetch();
			return $row ? $row['data'] : '';
		} catch (Exception $e) {
			return false;
		}
	}
	public function write(string $id, string $data): bool
	{
		try {
			$stmt = $this->db->prepare('REPLACE INTO `session` (`id`, `data`, `time`) VALUES (?, ?, ?)');
			$stmt->execute([$id, $data, date('Y-m-d H:i:s')]);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function destroy(string $id): bool
	{
		try {
			$stmt = $this->db->prepare('DELETE FROM `session` WHERE `id` = ?');
			$stmt->execute([$id]);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	public function gc(int $maxlifetime): int|false
	{
		try {
			$stmt = $this->db->prepare('DELETE FROM `session` WHERE `time` < ?');
			$time = date('Y-m-d H:i:s', time() - $maxlifetime);
			$stmt->execute([$time]);
			return $stmt->rowCount();
		} catch (Exception $e) {
			return false;
		}
	}
}
