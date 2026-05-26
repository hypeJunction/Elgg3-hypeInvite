<?php
/**
 *
 */

namespace hypeJunction\Invite;

use Elgg\Event;
use Exception;

/** Generate registration link with invite code. */
class GenerateRegistrationUrl {

	/**
	 * Generate registration link
	 *
	 * @elgg_event registration_url site
	 *
	 * @param Event $event Hook
	 *
	 * @return string
	 * @throws Exception
	 */
	public function __invoke(Event $event) {

		$registration_url = $event->getValue();

		if (!$registration_url) {
			$registration_url = \elgg_normalize_url('register');
		}

		$email = $event->getParam('email');

		if (!$email) {
			return $registration_url;
		}

		$user_invite = users_invite_get_user_invite($email);
		if (!$user_invite) {
			$user_invite = users_invite_create_user_invite($email);
		}

		$friend_guid = $event->getParam('friend_guid');
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

		return \elgg_http_add_url_query_elements($registration_url, [
			'e' => $email,
			'ts' => $time,
			'friend_guid' => $friend_guid,
			'invitation_code' => $invite_codes[0],
		]);
	}
}
