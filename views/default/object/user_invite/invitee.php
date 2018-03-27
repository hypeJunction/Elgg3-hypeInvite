<?php

$item = elgg_extract('item', $vars);

if (!$item instanceof \hypeJunction\Invite\Invite) {
	return;
}

if ($item->name) {
	echo elgg_format_element('h3', [], $item->name);
}

echo elgg_view('output/url', [
	'href' => 'mailto:' . $item->email,
	'text' => $item->email,
]);
