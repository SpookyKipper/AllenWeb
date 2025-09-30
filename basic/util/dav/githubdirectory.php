<?php

namespace Allen\Basic\Util\Dav;

use Allen\Basic\Util\Integration\GitHub;
use Sabre\DAV\{Collection, Exception\NotFound, Exception\Forbidden};

class GitHubDirectory extends Collection
{
	function __construct(
		private GitHub $github,
		private string $owner,
		private string $repo,
		private string $path = '',
	) {}
	private array|bool|null $_data = null;
	private function data(): array|bool
	{
		if ($this->_data === null) {
			$this->_data = $this->github->GetRepoContent(
				owner: $this->owner,
				repo: $this->repo,
				path: $this->path,
			);
		}
		return $this->_data;
	}
	function getChildren()
	{
		$data = $this->data();
		if ($data === false) {
			throw new NotFound('Repository not found or token invalid');
		} else if ($data['code'] !== 200) {
			throw new NotFound('Error: ' . json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} else if (!is_array($data['response'])) {
			throw new NotFound('No data found');
		}
		return array_values(array_map(fn($item) => $this->getChild($item['name']), array_filter($data['response'], fn($item) => isset($item['type'], $item['path']))));
	}
	function getChild($name)
	{
		$data = $this->data();
		if ($data === false) {
			throw new NotFound('Repository not found or token invalid');
		} else if ($data['code'] !== 200) {
			throw new NotFound('Error: ' . json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} else if (!is_array($data['response'])) {
			throw new NotFound('No data found');
		}
		$child = array_find($data['response'], fn($item) => basename($item['name'] ?? null) === $name);
		if ($child === null || !is_array($child) || !isset($child['type'], $child['path'])) {
			throw new NotFound('The file with name: ' . $name . ' could not be found');
		}
		return match ($child['type']) {
			'dir' => new GitHubDirectory(
				github: $this->github,
				owner: $this->owner,
				repo: $this->repo,
				path: $child['path'],
			),
			'file' => new GitHubFile(
				github: $this->github,
				owner: $this->owner,
				repo: $this->repo,
				path: $child['path'],
			),
			default => throw new NotFound('The file with name: ' . $name . ' could not be found'),
		};
	}
	function childExists($name)
	{
		$data = $this->data();
		if ($data === false || $data['code'] !== 200 || !is_array($data['response']) || array_find($data['response'], fn($item) => ($item['name'] ?? null) === $name) === null) {
			return false;
		}
		return true;
	}
	function getName()
	{
		return basename($this->path);
	}
	function createFile($name, $data = null)
	{
		if (is_resource($data)) {
			$data = stream_get_contents($data);
			if ($data === false) {
				$data = null;
			}
		}
		$result = $this->github->CreateOrUpdateFile(
			owner: $this->owner,
			repo: $this->repo,
			path: ($this->path === '' ? '' : $this->path . '/') . $name,
			commit_message: 'Create file ' . $name . ' via WebDAV',
			content: $data ?? '',
		);
		if ($result === false || $result['code'] !== 201) {
			throw new Forbidden('Error: ' . json_encode($result['response'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		}
		$this->_data = null;
		return isset($result['response']['content']['sha']) ? '"' . $result['response']['content']['sha'] . '"' : null;
	}
	function createDirectory($name)
	{
		$result = $this->github->CreateOrUpdateFile(
			owner: $this->owner,
			repo: $this->repo,
			path: ($this->path === '' ? '' : $this->path . '/') . $name . '/.gitkeep',
			commit_message: 'Create directory ' . $name . ' via WebDAV',
			content: '',
		);
		if ($result === false || $result['code'] !== 201) {
			throw new Forbidden('Error: ' . json_encode($result['response'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		}
		$this->_data = null;
	}
}
