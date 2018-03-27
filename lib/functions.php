<?php

use hypeJunction\Invite\Invite;
use hypeJunction\Invite\InviteService;

/**
 * Creates a new user invite
 *
 * @param string $email Email address
 *
 * @return Invite
 */
function users_invite_create_user_invite($email) {
	$svc = elgg()->{'users.invites'};

	/* @var $svc InviteService */

	return $svc->createInvite($email);
}

/**
 * Returns an invite object
 *
 * @param string $email Email address
 *
 * @return Invite|false
 */
function users_invite_get_user_invite($email) {
	$svc = elgg()->{'users.invites'};

	/* @var $svc InviteService */

	return $svc->getInvite($email);
}

/**
 * Returns a group invite object
 *
 * @param string $email Email address
 *
 * @return ElggObject|false
 */
function groups_invite_get_group_invite($email) {
	return users_invite_get_user_invite($email);
}

/**
 * Creates a new group invite
 *
 * @param string $email Email address
 *
 * @return ElggObject
 */
function groups_invite_create_group_invite($email) {
	return users_invite_create_user_invite($email);
}

/**
 * Generate a registration link
 *
 * @param string $email        Email address of the invitee
 * @param int    $inviter_guid GUID of the inviting user
 * @param array  $params       Additional params
 *
 * @return string
 */
function users_invite_get_registration_link($email, $inviter_guid = null, array $params = []) {
	$url = elgg_get_registration_url(array_merge($params, [
		'email' => $email,
		'friend_guid' => $inviter_guid,
	]));

	return elgg_normalize_url($url);
}