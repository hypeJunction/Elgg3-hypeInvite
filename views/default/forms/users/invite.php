<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'plaintext',
	'name' => 'emails',
	'#label' => elgg_echo('users:invite:emails:select'),
	'#help' => elgg_echo('users:invite:emails:select:help'),
	'rows' => 3,
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'name' => 'message',
	'#label' => elgg_echo('users:invite:message'),
	'rows' => 3,
]);

echo elgg_view_field([
	'#type' => 'checkbox',
	'name' => 'resend',
	'default' => false,
	'label' => elgg_echo('users:invite:resend'),
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('users:invite'),
]);

elgg_set_form_footer($footer);

