<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggEntity) {
	return;
}

$title = elgg_extract('title', $vars, '');
if ($title === '') {
	$title = elgg_view('output/url', [
		'text' => elgg_get_excerpt($entity->getDisplayName(), 100),
		'href' => $entity->getURL(),
	]);
}

$icon = elgg_extract('icon', $vars);
if (!isset($icon)) {
	$icon = elgg_view_entity_icon($entity, 'tiny');
}

echo elgg_view_image_block($icon, $title);
