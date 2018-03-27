<?php

$item = elgg_extract('item', $vars);

if (!$item instanceof \hypeJunction\Invite\Invite) {
	return;
}

$menu = elgg()->menus->getUnpreparedMenu('entity', [
	'entity' => $item,
]);

echo elgg_view_menu('user_invite', [
	'items' => $menu->getItems(),
	'entity' => $item,
	'class' => 'elgg-menu-hz elgg-menu-entity',
]);