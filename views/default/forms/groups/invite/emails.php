<?php

$entity = elgg_extract('entity', $vars);

echo elgg_view_field(array(
	'#type' => 'plaintext',
	'name' => 'emails',
	'#label' => elgg_echo('groups:invite:emails:select'),
	'help' => elgg_echo('groups:invite:emails:select:help'),
	'rows' => 3,
));