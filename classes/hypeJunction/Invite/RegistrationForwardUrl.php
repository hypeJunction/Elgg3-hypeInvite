<?php

namespace hypeJunction\Invite;

use Elgg\Hook;
use Exception;

class RegistrationForwardUrl {

	/**
	 * Hijack forward URL of the register action
	 *
	 * @elgg_plugin_hook register user
	 *
	 * @param Hook $hook Hook
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(Hook $hook) {

		$value = $hook->getValue();

		if ($value === false) {
			// Registration was prevented
			return;
		}

		$user = $hook->getUserParam();
		/* @var $user \ElggUser */

		elgg_call(ELGG_IGNORE_ACCESS, function () use ($user) {
			if (!$user->isEnabled()) {
				return;
			}

			$forward_url = '';

			$ref = get_input('ref');
			$entity = get_entity($ref);

			if ($entity instanceof \ElggGroup) {
				if (elgg_get_plugin_setting('groups_accept_on_register', 'hypeInvite')) {
					$forward_url = $entity->getURL();
				} else if (elgg_is_active_plugin('groups')) {
					$forward_url = elgg_generate_url('collection:group:group:invitations', [
						'username' => $user->username,
					]);
				}
			} else if ($entity instanceof \ElggUser) {
				if (elgg_get_plugin_setting('friends_accept_on_register', 'hypeInvite')) {
					$forward_url = $entity->getURL();
				} else if (elgg_is_active_plugin('friend_request')) {
					$forward_url = elgg_normalize_url("friend_request/$user->username/received");
				}
			}

			if (!$forward_url) {
				return;
			}

			elgg_register_plugin_hook_handler('forward', 'all', function () use ($forward_url) {
				return $forward_url;
			});
		});
	}
}