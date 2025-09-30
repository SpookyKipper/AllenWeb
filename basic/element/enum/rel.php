<?php

namespace Allen\Basic\Element\Enum;

enum Rel: string
{
	case Alternate = 'alternate';
	case Author = 'author';
	case Bookmark = 'bookmark';
	case Canonical = 'canonical';
	case DnsPrefetch = 'dns-prefetch';
	case External = 'external';
	case Help = 'help';
	case Icon = 'icon';
	case License = 'license';
	case Manifest = 'manifest';
	case Me = 'me';
	case ModulePreload = 'modulepreload';
	case Next = 'next';
	case NoFollow = 'nofollow';
	case NoOpener = 'noopener';
	case NoReferrer = 'noreferrer';
	case Opener = 'opener';
	case PingBack = 'pingback';
	case Preconnect = 'preconnect';
	case Prefetch = 'prefetch';
	case Prerender = 'prerender';
	case Prev = 'prev';
	case Search = 'search';
	case Stylesheet = 'stylesheet';
	case Tag = 'tag';
}
