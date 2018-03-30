<?php

$required = (bool) elgg_get_plugin_setting('invite_only_network', 'hypeInvite');
$show_field = elgg_get_plugin_setting('invite_code_register_form', 'hypeInvite', true);
$requests_allowed = (bool) elgg_get_plugin_setting('request_invitation', 'hypeInvite', $required);

$help = '';
if ($requests_allowed && $required) {
	$link = elgg_view('output/url', [
		'href' => elgg_generate_url('invite:request'),
		'text' => elgg_echo('users:invite:request'),
	]);

	$help = elgg_echo('users:invite:invite_only_network:request', [$link]);
}

if ($show_field) {
	echo elgg_view_field([
		'#type' => 'text',
		'name' => 'invitation_code',
		'value' => elgg_extract('invitation_code', $vars, get_input('invitation_code')),
		'#label' => elgg_echo('users:invite:invitation_code'),
		'required' => $required,
		'#help' => $help,
	]);
} else {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'invitation_code',
		'value' => elgg_extract('invitation_code', $vars, get_input('invitation_code')),
	]);
}

// Referring entity (group or user)
// Will be used to determine forward URL upon registration
echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'ref',
	'value' => elgg_extract('ref', $vars, get_input('ref')),
]);
