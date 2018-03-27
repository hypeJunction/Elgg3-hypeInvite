<?php

namespace hypeJunction\Invite;

use Elgg\EntityNotFoundException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class ConfirmInviteAction {

	/**
	 * Request an invitation
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws EntityNotFoundException
	 */
	public function __invoke(Request $request) {

		$invite_request = $request->getEntityParam('guid');

		if (!$invite_request instanceof InviteRequest) {
			throw new EntityNotFoundException();
		}

		$inviter = elgg_get_logged_in_user_entity();

		$users = get_user_by_email($invite_request->email);

		$sent = false;

		if ($users) {
			if (elgg_is_active_plugin('friend_request') && !$inviter->isFriendsWith($users[0]->guid)) {
				add_entity_relationship($inviter->guid, 'friendrequest', $users[0]->guid);
			}

			$sent = true;
		} else {
			$user_invite = users_invite_get_user_invite($invite_request->email);

			if (!$user_invite) {
				$user_invite = users_invite_create_user_invite($invite_request->email);
			}

			$link = users_invite_get_registration_link($invite_request->email, $inviter->guid, $request->getParam('params', []));

			$link = elgg_http_add_url_query_elements($link, [
				'ref' => $inviter->guid,
			]);

			$invite_codes = (array) $user_invite->invite_codes;

			add_entity_relationship($user_invite->guid, 'invited_by', $inviter->guid);

			$show_invite_code = elgg_get_plugin_setting('invite_code_register_form', 'hypeInvite', true);

			$site = elgg_get_site_entity();

			$notification_params = [
				'inviter' => $inviter->getDisplayName(),
				'site' => $site->getDisplayName(),
				'message' => '',
				'link' => $link,
				'invite_code' => $invite_codes[0],
				'message_code' => ($show_invite_code) ? elgg_echo('users:invite:notify:invite_code', [$invite_codes[0]]) : '',
			];

			$subject = elgg_echo('users:invite:notify:subject', [$site->getDisplayName()]);
			$body = elgg_echo('users:invite:notify:body', $notification_params);

			elgg_send_email($site->email, $invite_request->email, $subject, $body);

			$sent = true;
		}

		if ($sent) {
			$invite_request->delete();

			return elgg_ok_response('', elgg_echo('users:invite:request:confirm_invitation:success'));
		}

		return elgg_error_response(elgg_echo('users:invite:request:confirm_invitation:error'));

	}
}