<?php

namespace hypeJunction\Invite;

use Elgg\Event;
use Exception;

class ProcessUserInvitesOnRegistration {

	/**
	 * Accept invites when user is created
	 *
	 * @elgg_event create user
	 *
	 * @param Event $event Event
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(Event $event) {

		$user = $event->getObject();

		if (!$user instanceof \ElggUser) {
			return;
		}

		$email = $user->email;
		$code = get_input('invitation_code');

		elgg_call(ELGG_IGNORE_ACCESS, function() use ($user, $email, $code) {
			$svc = elgg()->{'users.invites'};
			/* @var $svc \hypeJunction\Invite\InviteService */

			if ($svc->validateInviteCode($email, $code)) {
				// Consider user email validated when joined via invitation email
				$user->setValidationStatus(true, 'invitation_code');
			}

			$invites = new \ElggBatch('elgg_get_entities', [
				'types' => 'object',
				'subtypes' => ['user_invite', 'group_invite'],
				'metadata_name_value_pairs' => [
					'name' => 'email',
					'value' => $email,
				],
				'limit' => 0,
			]);

			$invites->setIncrementOffset(false);

			foreach ($invites as $invite) {
				$params = [
					'invite' => $invite,
					'user' => $user,
				];
				if (elgg_trigger_plugin_hook('accept', 'invite', $params, true)) {
					$invite->delete();
				}
			}
		});
	}
}