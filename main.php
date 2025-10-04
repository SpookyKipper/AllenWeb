<?php
if (is_file(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}
spl_autoload_register(function ($class) {
	$prefix = 'Allen\\';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	$file = __DIR__ . '/' . strtolower(str_replace('\\', '/', substr($class, $len)));
	if (is_file($file . '.php')) {
		require_once $file . '.php';
	} else if (is_file($file . '/main.php')) {
		require_once $file . '/main.php';
	}
});
class_alias('Allen\\Web', 'Allen\\Basic\\Web');
array_map(function ($type) {
	if (!function_exists('allen_' . $type)) {
		require_once __DIR__ . '/basic/allen/' . $type . '.php';
	}
}, ['button', 'code', 'json', 'list', 'share', 'url']);
