<?php

if (!elgg_get_plugin_setting('invite_friends', 'hypeInvite')) {
	throw new \Elgg\PageNotFoundException();
}

$username = elgg_extract('username', $vars);
$user = get_user_by_username($username);

if (!$user || !$user->canEdit()) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_set_page_owner_guid($user->guid);

$title = elgg_echo('users:invite');

elgg_push_breadcrumb(elgg_echo('friends'), "friends");
elgg_push_breadcrumb($user->getDisplayName(), "friends/{$user->username}");
elgg_push_breadcrumb($title);

$content = elgg_view_form('users/invite', [], [
	'entity' => $user,
]);

$layout = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'filter_id' => 'friends',
	'filter_value' => 'invite',
]);

echo elgg_view_page($title, $layout);
