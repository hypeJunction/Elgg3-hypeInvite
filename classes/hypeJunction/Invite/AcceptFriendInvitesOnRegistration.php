<?php

namespace hypeJunction\Invite;

use Elgg\Hook;

class AcceptFriendInvitesOnRegistration {

	/**
	 * Create friendships or friendship requests when the invite is accepted
	 *
	 * @elgg_plugin_hook accept invite
	 *
	 * @param Hook $hook Hook
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function __invoke(Hook $hook) {
		$return = $hook->getValue();
		$params = $hook->getParams();

		if ($return === false) {
			return;
		}

		$invite = elgg_extract('invite', $params);
		$user = elgg_extract('user', $params);

		elgg_call(ELGG_IGNORE_ACCESS, function () use ($invite, $user) {
			$inviters = new \ElggBatch('elgg_get_entities', [
				'types' => 'user',
				'relationship' => 'invited_by',
				'relationship_guid' => (int) $invite->guid,
				'inverse_relationship' => false,
				'limit' => 0,
			]);

			$accept_on_register = elgg_get_plugin_setting('friends_accept_on_register', 'hypeInvite');

			// We will respect friend_request setting for river events
			$add_to_river = true;
			$relationship = 'friend';
			if (elgg_is_active_plugin('friend_request')) {
				$add_to_river = elgg_get_plugin_setting('add_river', 'friend_request') !== 'no';
				$relationship = 'friendrequest';
			}

			$ref = get_input('ref');

			foreach ($inviters as $inviter) {
				/* @var $inviter \ElggUser */

				if ($inviter->isFriendsWith($user->guid)) {
					continue;
				}

				if ($accept_on_register || $ref == $user->guid) {
					$inviter->addFriend($user->guid, $add_to_river);
					$user->addFriend($inviter->guid, $add_to_river);
				} else {
					add_entity_relationship($inviter->guid, $relationship, $user->guid);
				}
			}

		});
	}
}