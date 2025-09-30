<?php

namespace Allen\Basic\Util;

use Sabre\DAV\{Server, FS\Directory, INode, ServerPlugin, Tree};
use Allen\Basic\Util\Dav\GitHubDirectory;
use Allen\Basic\Util\Integration\GitHub;

class Dav
{
	public readonly Server $server;
	public function __construct(
		public readonly Tree|INode|array|null $root = null,
		public readonly ?string $baseUri = null,
	) {
		$this->server = new Server($root);
		if (!is_null($baseUri)) $this->server->setBaseUri($baseUri);
	}
	public function Run(): void
	{
		$this->server->start();
	}
	public function PluginAdd(ServerPlugin ...$plugins): self
	{
		foreach ($plugins as $plugin) {
			$this->server->addPlugin($plugin);
		}
		return $this;
	}
	public static function Directory(
		string $path,
		?string $baseUri = null,
	): self {
		return new self(
			root: new Directory($path),
			baseUri: $baseUri,
		);
	}
	public static function GitHub(
		GitHub $github,
		string $owner,
		string $repo,
		?string $path = '',
		?string $baseUri = null,
	): self {
		return new self(
			root: new GitHubDirectory(
				github: $github,
				owner: $owner,
				repo: $repo,
				path: $path,
			),
			baseUri: $baseUri,
		);
	}
}
