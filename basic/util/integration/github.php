<?php

namespace Allen\Basic\Util\Integration;

use Allen\Basic\Util\Config;
use Allen\Basic\Util\Request;
use Allen\Basic\Util\Request\RequestCache;

class GitHub
{
	public static function FromConfig(): self
	{
		return new self(
			token: Config::Get('util.github.token', null),
			token_expire: Config::Get('util.github.token_expire', null),
			repos: Config::Get('util.github.repos', null),
		);
	}
	public function __construct(private ?string $token = null, private ?int $token_expire = null, private ?array $repos = null)
	{}
	protected function _CheckRepo(string $owner, string $repo): bool
	{
		if (is_null($this->repos) || (isset($this->repos[$owner]) && is_array($this->repos[$owner]) && (in_array($repo, $this->repos[$owner]) || (array_key_exists($repo, $this->repos[$owner]) && is_string($repo))))) {
			return true;
		}
		return false;
	}
	protected function _Request(string $path, array $header = [], ?string $cacheId = null, int $cacheExpire = 0): Request|RequestCache
	{
		if (!is_null($this->token) && (is_null($this->token_expire) || $this->token_expire > time())) {
			$header['Authorization'] = 'Bearer ' . $this->token;
		}
		$header['User-Agent'] = 'AS_Allen_Shih-GH/1.1';
		$header['X-GitHub-Api-Version'] = '2022-11-28';
		if (is_null($cacheId)) {
			return new Request('https://api.github.com' . $path, $header);
		}
		return new RequestCache($cacheId, $cacheExpire, 'https://api.github.com' . $path, $header);
	}
	public function GetRepoContent(string $owner, string $repo, string $path = '', ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		}
		if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/contents/$path",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->GET()) {
			return false;
		}
		if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/contents/$path",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
		)->GET()) {
			return false;
		}
		return $response;
	}
	public function CreateOrUpdateFile(string $owner, string $repo, string $path, string $commit_message, string $content, bool $check_exist = true, ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		}
		$data = [
			'message' => $commit_message,
			'content' => base64_encode($content),
		];
		if ($check_exist) {
			$repository_data = $this->GetRepoContent($owner, $repo, $path);
			if ($repository_data && $repository_data['code'] === 200 && $repository_data['response']['type'] === 'file' && isset($repository_data['response']['sha'])) {
				$data['sha'] = $repository_data['response']['sha'];
			}
		}
		if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/contents/$path",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->PUT(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))) {
			return false;
		}
		return $response;
	}
	public function GetReleases(string $owner, string $repo, int $page = 1, int $per_page = 30, ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		}
		if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/releases?page=$page&per_page=$per_page",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->GET()) {
			return false;
		}
		return $response;
	}
	public function GetReleaseLatest(string $owner, string $repo, ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		} else if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/releases/latest",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->GET()) {
			return false;
		}
		return $response;
	}
	public function GetRelease(string $owner, string $repo, int $release_id, ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		} else if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/releases/$release_id",
			header: [
				'Accept' => 'application/vnd.github+json',
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->GET()) {
			return false;
		}
		return $response;
	}
	public function GetReleaseAsset(string $owner, string $repo, int $asset_id, bool $download = true, ?string $cacheId = null, int $cacheExpire = 0): array|bool
	{
		if (!$this->_CheckRepo($owner, $repo)) {
			return false;
		} else if (false === $response = $this->_Request(
			path: "/repos/$owner/$repo/releases/assets/$asset_id",
			header: [
				'Accept' => 'application/' . ($download ? 'octet-stream' : 'vnd.github+json'),
			],
			cacheId: $cacheId,
			cacheExpire: $cacheExpire,
		)->GET()) {
			return false;
		}
		return $response;
	}
}
