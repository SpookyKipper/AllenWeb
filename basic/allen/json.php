<?php
/**
 * 輸出JSON
 * @param mixed $data 資料
 */
function allen_json($data)
{
	header('Content-Type: application/json; charset=UTF-8');
	$json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	header('Content-Length: ' . strlen($json_data));
	echo $json_data;
	die();
}