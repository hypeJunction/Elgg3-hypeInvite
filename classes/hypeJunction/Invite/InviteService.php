<?php

namespace hypeJunction\Invite;

use ElggBatch;
use ElggUser;
use Exception;

class InviteService {

	/**
	 * Creates a new user invite
	 *
	 * @param string $email Email address
	 *
	 * @return Invite
	 * @throws Exception
	 */
	public function createInvite($email) {
		$user_invite = users_invite_get_user_invite($email);
		if ($user_invite) {
			return $user_invite;
		}

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			$site = elgg_get_site_entity();

			$user_invite = new Invite();
			$user_invite->subtype = 'user_invite';
			$user_invite->owner_guid = $site->guid;
			$user_invite->container_guid = $site->guid;
			$user_invite->access_id = ACCESS_PUBLIC;
			$user_invite->email = $email;

			$user_invite->invite_codes = $this->generateInviteCode();

			$user_invite->save();

			return $user_invite;
		});
	}

	/**
	 * Returns an invite object
	 *
	 * @param string $email Email address
	 *
	 * @return Invite|false
	 * @throws Exception
	 */
	public function getInvite($email) {

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($email) {
			$invites = elgg_get_entities([
				'types' => 'object',
				'subtypes' => 'user_invite',
				'metadata_name_value_pairs' => [
					'email' => $email,
				],
				'limit' => 1,
			]);

			return $invites ? $invites[0] : false;
		});
	}

	/**
	 * Returns an invite object
	 *
	 * @param string $invite_code Invite code
	 *
	 * @return Invite|false
	 * @throws Exception
	 */
	public function getInviteByCode($invite_code) {

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($invite_code) {
			$invites = elgg_get_entities([
				'types' => 'object',
				'subtypes' => 'user_invite',
				'metadata_name_value_pairs' => [
					'name' => 'invite_codes',
					'value' => $invite_code,
				],
				'limit' => 1,
			]);

			return $invites ? $invites[0] : false;
		});
	}

	/**
	 * Generate a unique invite code
	 * @return string
	 * @throws Exception
	 */
	public function generateInviteCode() {
		$invite_code = generate_random_cleartext_password();

		while ($this->getInviteByCode($invite_code)) {
			$invite_code = generate_random_cleartext_password();
		}

		return $invite_code;
	}

	/**
	 * Validate invite code
	 *
	 * @param string $email       Email address
	 * @param string $invite_code Invitation code
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function validateInviteCode($email, $invite_code) {

		$invitation_codes = elgg_get_plugin_setting('invitation_codes', 'hypeInvite');

		if ($invitation_codes) {
			$invitation_codes = explode(PHP_EOL, $invitation_codes);
			array_walk($invitation_codes, 'trim');
			if (in_array($invite_code, $invitation_codes)) {
				return true;
			}
		}

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($email, $invite_code) {
			$invites = elgg_get_entities([
				'types' => 'object',
				'subtypes' => 'user_invite',
				'metadata_name_value_pairs' => [
					[
						'name' => 'email',
						'value' => $email,
					],
					[
						'name' => 'invite_codes',
						'value' => $invite_code,
					]
				],
				'count' => true,
			]);

			return $invites > 0;
		});
	}

}
