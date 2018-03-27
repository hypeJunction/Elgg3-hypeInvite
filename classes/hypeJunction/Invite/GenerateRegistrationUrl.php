<?php
/**
 *
 */

namespace hypeJunction\Invite;


use Elgg\Hook;
use Exception;

class GenerateRegistrationUrl {

	/**
	 * Generate registration link
	 *
	 * @elgg_plugin_hook registration_url site
	 *
	 * @param Hook $hook Hook
	 *
	 * @return string
	 * @throws Exception
	 */
	public function __invoke(Hook $hook) {

		$registration_url = $hook->getValue();

		if (!$registration_url) {
			$registration_url = elgg_normalize_url('register');
		}

		$email = $hook->getParam('email');

		if (!$email) {
			return $registration_url;
		}

		$user_invite = users_invite_get_user_invite($email);
		if (!$user_invite) {
			$user_invite = users_invite_create_user_invite($email);
		}

		$friend_guid = $hook->getParam('friend_guid');
		if ($friend_guid) {
			add_entity_relationship($user_invite->guid, 'invited_by', $friend_guid);
		}

		if (!$user_invite->invite_codes) {
			$invite_code = generate_random_cleartext_password();

			$svc = elgg()->{'users.invites'};
			/* @var $svc \hypeJunction\Invite\InviteService */

			while ($svc->getInviteByCode($invite_code)) {
				$invite_code = generate_random_cleartext_password();
			}

			$user_invite->invite_codes = $invite_code;
		}

		$time = time();

		$invite_codes = (array) $user_invite->invite_codes;

		return elgg_http_add_url_query_elements($registration_url, [
			'e' => $email,
			'ts' => $time,
			'friend_guid' => $friend_guid,
			'invitecode' => $invite_codes[0],
		]);
	}
}