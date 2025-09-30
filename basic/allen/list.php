<?php
/**
 * 輪轉列表
 * @param string|int $id 列表ID
 * @param array $config [width: string, height: string, time: float, opacity: float]
 * @param array $lists [image: string, alt: string, element: string, background: string, description: string, link: string, new: bool, opacity: float] or string
 */
function allen_list($id, $config = null, ...$lists)
{
	$output = [];
	foreach ($lists as $list) {
		$element = '<div style="min-width: 100%; margin: 0;">';
		if (is_array($list)) {
			if (isset($list['link'])) {
				$element .= '<a href="' . $list['link'] . '"' . ((isset($list['new']) && $list['new']) ? ' target="_blank"' : '') . ' style="display: block; text-decoration: auto;">';
			}
		}
		$element .= '<div style="display: block; position: static; max-width: 100%; max-height: calc(100vh - 40px); width: ' . (isset($config['width']) ? $config['width'] : '100%') . '; height: ' . (isset($config['height']) ? $config['height'] : '100%') . ';">';
		if (is_array($list)) {
			if (isset($list['image'])) {
				$element .= '<img src="' . $list['image'] . '" alt="' . ($list['alt'] ?? '') . '" style="max-width: 100%; max-height: calc(100vh - 40px); z-index: 0;"><link rel="preload" href="' . $list['image'] . '" as="image">';
			}
			if (isset($list['element'])) {
				$element .= '<div style="max-width: 100%; max-height: calc(100vh - 36px);' . (isset($list['image']) ? ' position: absolute; top: 0;' : '') . 'min-height: fix-content; z-index: 1; display: block;">' . $list['element'] . '</div>';
			}
			if (isset($list['background'])) {
				$element .= '<div style="' . (isset($list['image']) || isset($list['element']) ? ' position: absolute; top: 0;' : '') . ' opacity: ' . ($list['opacity'] ?? $config['opacity'] ?? 1) . ' !important; background-image: url(\'' . $list['background'] . '\'); background-repeat: no-repeat; background-position: center; background-size: cover; width: 100%; height: 100%; z-index: -1; display: block;"></div><link rel="preload" href="' . $list['background'] . '" as="image">';
			}
		} else {
			$element .= '<img src="' . $list . '" style="max-width: 100%; max-height: calc(100vh - 40px);"><link rel="preload" href="' . $list . '" as="image">';
		}
		$element .= '</div>';
		if (is_array($list)) {
			if (isset($list['description'])) {
				$element .= '<div style="background-color: var(--backgroundColor)"><p style="margin: 0;">' . $list['description'] . '</p></div>';
			}
			if (isset($list['link'])) {
				$element .= '</a>';
			}
		}
		$element .= '</div>';
		$output[] = $element;
	}
	$list_count = count($output);
	$output = implode('', $output);
	$main_style = '--list-n: 0; width: 100%; display: flex; flex-direction: row; transform: translateX(calc(var(--list-n) * -100%)); transition: all 0.5s; padding: 0; margin: 0; align-items: flex-start;';
	$sub_style = 'display: flex; justify-content: space-around; align-items: center; background-color:#777777;';
	$title = '<div style="' . $sub_style . '"><span><span id="list-' . $id . '-now_text">1</span>/<span>' . $list_count . '</span></span></div>';
	$controller = '<div style="' . $sub_style . '"><button id="list-' . $id . '-prev" type="button" class="material-symbols-outlined">arrow_back</button><button id="list-' . $id . '-play_pause" type="button" class="material-symbols-outlined">play_arrow</button><button id="list-' . $id . '-next" type="button" class="material-symbols-outlined">arrow_forward</button></div>';
	$script = '
function listMain() {
	const listDivElement = document.getElementById("list-' . $id . '");
	if (!listDivElement) {
		return;
	}
	let listN = 0;
	let list = true;
	const listTitleNowText = document.getElementById("list-' . $id . '-now_text");
	function listShow(imgId) {
		listN = imgId % ' . $list_count . ';
		if (listN < 0) {
			listN = ' . ($list_count - 1) . '
		}
		listDivElement.style.setProperty("--list-n", listN);
		if (listTitleNowText) {
			listTitleNowText.textContent = listN + 1;
		}
	}
	function listAdd(add) {
		listShow(listN + add);
	}
	setInterval(() => {
		if (!list) {
			if (list === null) {
				list = true;	
			} return;
		}
		listAdd(1);
	}, ' . (($config['time'] ?? 5) * 1000) . ');
	const prev = document.getElementById("list-' . $id . '-prev");
	if (prev) {
		prev.onclick = () => {
			if (list === true) {
				list = null;
			}
			listAdd(-1);
		}
	}
	const next = document.getElementById("list-' . $id . '-next");
	if (next) {
		next.onclick = () => {
			if (list === true) {
				list = null;
			}
			listAdd(1);
		}
	}
	const playPause = document.getElementById("list-' . $id . '-play_pause");
	if (playPause) {
		playPause.onclick = () => {
			if (list === false) {
				list = true;
				playPause.textContent = "play_arrow";
			} else {
				list = false;
				playPause.textContent = "pause";
			}
		}
	}
	let startX = 0;
	let startY = 0;
 	listDivElement.addEventListener("touchstart", (event) => {
		startX = event.touches[0].pageX ?? 0;
		startY = event.touches[0].pageY ?? 0;
	});
  	listDivElement.addEventListener("touchend", (event) => {
		const endX = event.changedTouches[0].pageX ?? 0;
		const endY = event.changedTouches[0].pageY ?? 0;
		const spanX = endX - startX;
		const spanY = endY - startY;
  		console.log(spanX, spanY);
		if (Math.abs(spanX) > Math.abs(spanY)) {
			if (spanX > 32) {
   				listAdd(-1);
				if (list === true) {
					list = null;
				}
			} else if (spanX < -32) {
   				listAdd(1);
       				if (list === true) {
					list = null;
				}
			}
		}
	});
}
window.addEventListener("load", () => {
	listMain();
});
';
	return '<div style="overflow: hidden;">
	' . $title . '<div id="list-' . $id . '" style="' . $main_style . '">' . $output . '</div>' . $controller . '
	<script type="module">' . $script . '</script>
</div>';
}
