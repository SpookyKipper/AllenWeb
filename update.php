<?php
// 在各個子資料夾執行 git pull
$folders = [
	'apis',
	'id',
	'xhost/dash',
];
foreach ($folders as $folder) {
	if (!is_dir(__DIR__ . '/' . $folder)) {
		continue;
	}
	echo "Pulling in folder: $folder" . PHP_EOL;
	chdir(__DIR__ . '/' . $folder);
	exec('git pull', $output, $return_var);
	if ($return_var !== 0) {
		echo "Error pulling in folder $folder: " . implode(PHP_EOL, $output) . PHP_EOL;
	} else {
		echo "Successfully pulled in folder $folder: " . implode(PHP_EOL, $output) . PHP_EOL;
	}
}
chdir(__DIR__);
echo 'Pulling in main folder' . PHP_EOL;
exec('git pull', $output, $return_var);
if ($return_var !== 0) {
	echo 'Error pulling in main folder: ' . implode(PHP_EOL, $output) . PHP_EOL;
} else {
	echo 'Successfully pulled in main folder: ' . implode(PHP_EOL, $output) . PHP_EOL;
}
echo 'Updating dependencies with Composer...' . PHP_EOL;
exec('composer install --no-dev' . (in_array('--optimize-autoloader', $argv) ? ' --optimize-autoloader' : ''), $output, $return_var);
if ($return_var !== 0) {
	echo 'Error updating dependencies: ' . implode(PHP_EOL, $output) . PHP_EOL;
} else {
	echo 'Successfully updated dependencies: ' . implode(PHP_EOL, $output) . PHP_EOL;
}
echo "Done." . PHP_EOL;
exit(0);
