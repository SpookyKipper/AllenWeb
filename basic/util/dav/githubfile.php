<?php

namespace Allen\Basic\Util\Dav;

use Allen\Basic\Util\Integration\GitHub;
use Sabre\DAV\{Exception\Forbidden, Exception\NotFound, File};

class GitHubFile extends File
{
	function __construct(
		private GitHub $github,
		private string $owner,
		private string $repo,
		private string $path,
	) {}
	private ?array $_data = null;
	private function data(): ?array
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
	function getName()
	{
		return basename($this->path);
	}
	function put($data)
	{
		$result = $this->github->CreateOrUpdateFile(
			owner: $this->owner,
			repo: $this->repo,
			path: $this->path,
			commit_message: 'Update file ' . $this->getName() . ' via WebDAV',
			content: $data,
		);
		if ($result === false || $result['code'] !== 200 && $result['code'] !== 201) {
			throw new Forbidden('Error: ' . json_encode($result['response'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		}
		$this->_data = null;
		if (is_array($result['response'] ?? null) && isset($result['response']['content']['sha'])) {
			return '"' . $result['response']['content']['sha'] . '"';
		}
		return null;
	}
	function get()
	{
		$data = $this->data();
		if ($data === false) {
			throw new NotFound('Repository not found or token invalid');
		} else if ($data['code'] !== 200) {
			throw new Forbidden('Error: ' . json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} else if (!is_array($data['response'])) {
			throw new NotFound('No data found');
		} else if (is_string($data['response']['encoding'] ?? null) && $data['response']['encoding'] === 'base64' && is_string($data['response']['content'] ?? null)) {
			return base64_decode($data['response']['content']);
		} else if (is_string($data['response']['download_url'] ?? null)) {
			$content = file_get_contents($data['response']['download_url']);
			if ($content === false) {
				throw new NotFound('Error downloading file from GitHub');
			}
			return $content;
		} else {
			throw new NotFound('No content found');
		}
	}
	function getSize()
	{
		$data = $this->data();
		if ($data === false) {
			throw new NotFound('Repository not found or token invalid');
		} else if ($data['code'] !== 200) {
			throw new NotFound('Error: ' . json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} else if (!is_array($data['response'])) {
			throw new NotFound('No data found');
		} else if (is_int($data['response']['size'] ?? null)) {
			return $data['response']['size'];
		} else {
			return strlen($this->get());
		}
	}
	function getETag()
	{
		$data = $this->data();
		if ($data === false) {
			throw new NotFound('Repository not found or token invalid');
		} else if ($data['code'] !== 200) {
			throw new NotFound('Error: ' . json_encode($data['response'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		} else if (!is_array($data['response'])) {
			throw new NotFound('No data found');
		} else if (is_string($data['response']['sha'] ?? null)) {
			return '"' . $data['response']['sha'] . '"';
		} else {
			return null;
		}
	}
}
