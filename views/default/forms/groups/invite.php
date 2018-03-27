<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup) {
	return;
}

$tabs = [];

if (elgg_get_plugin_setting('groups_users_tab', 'hypeInvite', false)) {
	$tabs['users'] = [
		'text' => elgg_echo('groups:invite:users'),
		'selected' => true,
		'content' => elgg_view('forms/groups/invite/users', $vars),
	];
} else {
	$tabs['friends'] = [
		'text' => elgg_echo('groups:invite:friends'),
		'selected' => true,
		'content' => elgg_view('forms/groups/invite/friends', $vars),
	];
}

if (elgg_get_plugin_setting('groups_emails_tab', 'hypeInvite', false) && elgg_get_config('allow_registration')) {
	$tabs['emails'] = [
		'text' => elgg_echo('groups:invite:emails'),
		'content' => elgg_view('forms/groups/invite/emails', $vars),
	];
}

echo elgg_view('page/components/tabs', [
	'tabs' => $tabs,
]);

echo elgg_view_field([
	'#type' => 'plaintext',
	'name' => 'message',
	'#label' => elgg_echo('groups:invite:message'),
	'rows' => 3,
]);

if ($entity->canEdit()) {
	echo elgg_view_field([
		'#type' => 'checkbox',
		'name' => 'resend',
		'default' => false,
		'label' => elgg_echo('groups:invite:resend'),
	]);
}

if ($entity->canEdit() && (!elgg_get_plugin_setting('groups_require_confirmation', 'hypeInvite') || elgg_is_admin_logged_in())) {
	echo elgg_view_field([
		'#type' => 'radio',
		'name' => 'invite_action',
		'value' => 'invite',
		'options' => [
			elgg_echo('groups:invite:action:invite') => 'invite',
			elgg_echo('groups:invite:action:add') => 'add'
		],
	]);
}

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'guid',
	'value' => $entity->guid,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('groups:invite'),
]);

elgg_set_form_footer($footer);

