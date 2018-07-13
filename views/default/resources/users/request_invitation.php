<?php

if (elgg_is_logged_in()) {
	throw new \Elgg\GatekeeperException();
}

$title = elgg_echo('users:invite:request');

$form_vars = ['class' => 'elgg-form-account'];
$content = elgg_view_form('users/request_invitation', $form_vars, []);

if (elgg_is_active_plugin('hypeTheme')) {
	$shell = 'walled_garden';
} else {
	$shell = elgg_get_config('walled_garden') ? 'walled_garden' : 'default';
}

$body = elgg_view_layout('default', [
	'content' => $content,
	'title' => $title,
	'sidebar' => false,
]);

echo elgg_view_page($title, $body, $shell);
