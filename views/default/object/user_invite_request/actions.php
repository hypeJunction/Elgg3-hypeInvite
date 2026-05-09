<?php

elgg_import_esm('js/object/user_invite_request/actions');

$item = elgg_extract('item', $vars);

if (!$item instanceof \hypeJunction\Invite\InviteRequest) {
	return;
}

$items = [
	[
		'name' => 'confirm',
		'text' => elgg_echo('users:invite:request:confirm_invitation'),
		'href' => elgg_generate_action_url('users/confirm_invitation', [
			'guid' => $item->guid,
		]),
		'icon' => 'check',
		'confirm' => true,
		'class' => 'elgg-button elgg-button-submit',

	],
	[
		'name' => 'delete',
		'text' => elgg_echo('delete'),
		'href' => elgg_generate_action_url('entity/delete', [
			'guid' => $item->guid,
		]),
		'icon' => 'times',
		'confirm' => true,
		'class' => 'elgg-button elgg-button-delete',


	]
];

echo elgg_view_menu('user_invite_request', [
	'items' => $items,
	'class' => 'elgg-menu-hz',
]);