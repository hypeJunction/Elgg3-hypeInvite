<?php

elgg_gatekeeper();

$guid = elgg_extract('guid', $vars);
elgg_set_page_owner_guid($guid);

elgg_entity_gatekeeper($guid, 'group');

$title = elgg_echo('groups:invite:title');

$group = get_entity($guid);
if (!$group instanceof ElggGroup) {
	elgg_register_error_message(elgg_echo('groups:noaccess'));
	elgg_redirect_response(REFERER);
}

if (!$group->canEdit() && (!$group->isMember() || $group->invites_enable !== 'yes')) {
	elgg_register_error_message(elgg_echo('groups:noaccess'));
	elgg_redirect_response(REFERER);
}

$content = elgg_view_form('groups/invite', [
	'id' => 'invite_to_group',
], [
	'entity' => $group,
]);

elgg_push_breadcrumb($group->name, $group->getURL());
elgg_push_breadcrumb(elgg_echo('groups:invite'));

$params = [
	'content' => $content,
	'title' => $title,
	'filter' => '',
];
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
