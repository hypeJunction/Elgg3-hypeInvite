<?php

$item = elgg_extract('item', $vars);

if (!$item instanceof \hypeJunction\Invite\Invite) {
	return;
}

echo elgg_list_entities([
	'types' => 'user',
	'relationship' => 'invited_by',
	'relationship_guid' => $item->guid,
	'inverse_relationship' => false,
	'item_view' => 'object/user_invite/chip',
	'limit' => 0,
]);