<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'autocomplete',
	'#label' => elgg_echo('groups:invite:users:select'),
	'multiple' => true,
	'name' => 'invitee_guids',
	'match_on' => 'non_group_members',
	'options' => [
		'group_guid' => $entity->guid,
		'friends_only' => false,
	],
]);