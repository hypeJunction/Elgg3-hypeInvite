<?php

echo elgg_view_message('error', elgg_echo('users:invite:invitation_code:empty'), [
	'title' => false,
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('users:invite:request:name'),
	'name' => 'name',
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'email',
	'#label' => elgg_echo('users:invite:request:email'),
	'name' => 'email',
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('users:invite:request:message'),
	'#help' => elgg_echo('users:invite:request:message:help'),
	'rows' => 3,
	'name' => 'message',
]);

echo elgg_view_field([
	'#type' => 'captcha',
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('send'),
]);

elgg_set_form_footer($footer);