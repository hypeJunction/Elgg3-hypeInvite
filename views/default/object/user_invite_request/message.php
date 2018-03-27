<?php

$item = elgg_extract('item', $vars);

if (!$item instanceof \hypeJunction\Invite\InviteRequest) {
	return;
}

echo elgg_view('output/longtext', [
	'value' => $item->message,
]);