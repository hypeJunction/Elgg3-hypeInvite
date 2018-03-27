<?php

echo elgg_list_entities([
	'types' => 'object',
	'subtypes' => ['user_invite', 'group_invite'],
	'list_type' => 'table',
	'columns' => [
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite/invitee'),
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite/invited_by'),
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite/invited_to'),
		new \Elgg\Views\TableColumn\ViewColumn('object/user_invite/menu', ''),
	],
	'no_results' => elgg_echo('users:invite:no_results'),
]);