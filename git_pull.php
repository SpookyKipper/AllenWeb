<?php
// 在各個子資料夾執行 git pull
$folders = [
	'api',
	'id',
	'xhost/dash',
];
foreach ($folders as $folder) {
	if (!is_dir(__DIR__ . '/' . $folder)) {
		continue;
	} else if (!is_dir(__DIR__ . '/' . $folder . '/.git')) {
		echo "Folder $folder is not a git repository, skipping.\n";
		continue;
	}
	echo "Pulling in folder: $folder\n";
	chdir(__DIR__ . '/' . $folder);
	exec('git pull', $output, $return_var);
	if ($return_var !== 0) {
		echo "Error pulling in folder $folder: " . implode(PHP_EOL, $output) . PHP_EOL;
	} else {
		echo "Successfully pulled in folder $folder: " . implode(PHP_EOL, $output) . PHP_EOL;
	}
}
chdir(__DIR__);
echo "Pulling in main folder\n";
exec('git pull', $output, $return_var);
if ($return_var !== 0) {
	echo "Error pulling in main folder: " . implode(PHP_EOL, $output) . PHP_EOL;
} else {
	echo "Successfully pulled in main folder: " . implode(PHP_EOL, $output) . PHP_EOL;
}
echo "Done." . PHP_EOL;
