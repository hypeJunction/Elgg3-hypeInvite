<?php

elgg_require_js('admin/users/requests');

$check_all = elgg_format_element('input', [
	'type' => 'checkbox',
	'target' => 'guids[]',
	'class' => 'elgg-table-check-all',
]);

echo elgg_list_entities([
	'types' => 'object',
	'subtypes' => \hypeJunction\Invite\InviteRequest::SUBTYPE,
	'list_type' => 'table',
	'columns' => [
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite_request/sender'),
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite_request/message'),
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite_request/actions', ''),
	],
	'no_results' => elgg_echo('users:invite:request:no_results'),
]);