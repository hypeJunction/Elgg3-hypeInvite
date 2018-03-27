<?php

$user = elgg_get_logged_in_user_entity();

echo elgg_view_form('users/invite', [], [
	'entity' => $user,
]);
