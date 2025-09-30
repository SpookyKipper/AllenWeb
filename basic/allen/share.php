<?php
function allen_share(string $url = null, string $text = null)
{
	$url = $url ?? ('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$share = [
		'Line' => [
			'base' => 'https://social-plugins.line.me/lineit/share',
			'image' => 'https://cdn.asallenshih.tw/image/share/line.png',
			'url' => 'url',
			'text' => 'text',
		],
		'Facebook' => [
			'base' => 'https://www.facebook.com/sharer/sharer.php',
			'image' => 'https://cdn.asallenshih.tw/image/share/facebook.png',
			'url' => 'u',
		],
	];
	$output = [];
	foreach ($share as $service_name => $service) {
		if (!is_array($service) || !is_string($service['base'] ?? null)) {
			continue;
		}
		$service_url = $service['base'];
		if (str_contains('?', $service_url)) {
			$service_url .= '&';
		} else {
			$service_url .= '?';
		}
		$output_query = [];
		if (is_string($service['url'] ?? null) && is_string($url)) {
			$output_query[] = $service['url'] . '=' . urlencode($url);
		}
		if (is_string($service['text'] ?? null) && is_string($text)) {
			$output_query[] = $service['text'] . '=' . urlencode($text);
		}
		$service_url .= implode('&', $output_query);
		$output_html = '<a href="' . $service_url . '" target="_blank" style="text-decoration: auto;" title="' . $service_name . '">';
		if (is_string($service['image'] ?? null)) {
			$output_html .= '<img width="20px" height="20px" src="' . $service['image'] . '" alt="' . $service_name . '">';
		} else {
			$output_html .= '<p>' . $service_name . '</p>';
		}
		$output_html .= '</a>';
		$output[] = $output_html;
	}
	return '<div class="card"><h3>分享</h3><div class="flex">'.implode('', $output).'</div></div>';
}