<?php

namespace hypeJunction\Invite;

use Elgg\EntityNotFoundException;
use Elgg\Http\OkResponse;
use Elgg\Request;
use NotificationException;

class InviteGroupMembersAction {

	/**
	 * Invite members to the group
	 *
	 * @param Request $request Request
	 *
	 * @return OkResponse
	 * @throws EntityNotFoundException
	 * @throws NotificationException
	 */
	public function __invoke(Request $request) {

		$group = $request->getEntityParam('guid');

		if (!$group instanceof \ElggGroup) {
			throw new EntityNotFoundException();
		}

		$inviter = elgg_get_logged_in_user_entity();

		$invitee_guids = $request->getParam('invitee_guids', []);
		$emails = (string) $request->getParam('emails', '');
		$resend = $request->getParam('resend', false);
		$add = $request->getParam('invite_action') == 'add';
		$message = $request->getParam('message', '');

		$skipped = 0;
		$invited = 0;
		$added = 0;
		$error = 0;

		if ($invitee_guids && !is_array($invitee_guids)) {
			$invitee_guids = string_to_tag_array($invitee_guids);
		}

		$emails = preg_split('/$\R?^/m', $emails);
		$emails = array_filter($emails);

		foreach ($emails as $email) {
			if (empty($email)) {
				continue;
			}

			$email = trim($email);

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$error++;
				continue;
			}

			$users = get_user_by_email($email);
			if ($users) {
				$invitee_guids[] = $users[0]->guid;
				continue;
			}

			if (!elgg_get_config('allow_registration')) {
				$error++;
				continue;
			}

			if (!elgg_get_plugin_setting('invite_groups', 'hypeInvite')) {
				$error++;
				continue;
			}

			$group_invite = users_invite_get_user_invite($email);
			if (!$group_invite) {
				$group_invite = users_invite_create_user_invite($email);
			}

			$new = true;
			if (check_entity_relationship($group_invite->guid, 'invited_to', $group->guid)) {
				$new = false;
			}

			if (!$new && !$resend) {
				$skipped++;
				continue;
			}

			add_entity_relationship($group_invite->guid, 'invited_by', $inviter->guid);
			add_entity_relationship($group_invite->guid, 'invited_to', $group->guid);

			$link = users_invite_get_registration_link($email, $inviter->guid, $request->getParam('params', []));
			$link = elgg_http_add_url_query_elements($link, [
				'ref' => $group->guid,
			]);

			$invite_codes = (array) $group_invite->invite_codes;

			$site = elgg_get_site_entity();

			$show_invite_code = elgg_get_plugin_setting('invite_code_register_form', 'hypeInvite', true);

			$notification_params = [
				'inviter' => $inviter->getDisplayName(),
				'group' => $group->getDisplayName(),
				'site' => $site->getDisplayName(),
				'message' => ($message) ? elgg_echo('groups:invite:notify:message', [$message]) : '',
				'link' => $link,
				'invite_code' => $invite_codes[0],
				'message_code' => ($show_invite_code) ? elgg_echo('groups:invite:notify:invite_code', [$invite_codes[0]]) : '',
			];

			$subject = elgg_echo('groups:invite:notify:subject', [$group->getDisplayName()]);
			$body = elgg_echo('groups:invite:notify:body', $notification_params);

			$sent = elgg_send_email($site->email, $email, $subject, $body);
			if ($sent) {
				$invited++;
			} else {
				$error++;
			}
		}

		foreach ($invitee_guids as $invitee_guid) {
			if (!$invitee_guid) {
				continue;
			}

			$invitee = get_entity($invitee_guid);
			if (!$invitee instanceof \ElggUser) {
				$error++;
				continue;
			}

			if (check_entity_relationship($invitee->guid, 'member', $group->guid)) {
				$skipped++;
				continue;
			}

			if ($add) {
				if ($group->canEdit() && $group->join($invitee)) {
					$added++;
				} else {
					$error++;
				}
				continue;
			}

			if (check_entity_relationship($group->guid, 'invited', $invitee->guid)) {
				if (!$resend) {
					$skipped++;
					continue;
				}
			}

			add_entity_relationship($group->guid, 'invited', $invitee->guid);

			$hmac = elgg_build_hmac([
				'i' => (int) $invitee->guid,
				'g' => (int) $group->guid,
			]);

			$url = elgg_generate_url('invite:group:group:confirm', [
				'i' => $invitee->guid,
				'g' => $group->guid,
				'm' => $hmac->getToken(),
			]);

			$invitee_link = elgg_view('output/url', [
				'text' => $inviter->getDisplayName(),
				'href' => $inviter->getURL(),
			]);

			$group_link = elgg_view('output/url', [
				'text' => $group->getDisplayName(),
				'href' => $group->getURL(),
			]);

			$summary = elgg_echo('groups:invite:user:subject', [
				$invitee_link,
				$group_link,
			], $invitee->language);

			$subject = strip_tags($summary);

			$body = elgg_echo('groups:invite:user:body', [
				$invitee->name,
				$inviter->name,
				$group->name,
				$url,
			], $invitee->language);

			$params = [
				'action' => 'invite',
				'object' => $group,
				'summary' => $summary,
				'template' => 'groups_invite_user',
				'links' => [
					'confirm' => $url,
				],
				'url' => elgg_generate_url('collection:group:group:invitations', [
					'username' => $invitee->username,
				]),
			];

			$result = notify_user($invitee->getGUID(), $inviter->guid, $subject, $body, $params);
			if ($result) {
				$invited++;
			} else {
				$error++;
			}
		}

		$total = $error + $invited + $skipped + $added;
		if ($invited) {
			system_message(elgg_echo('groups:invite:result:invited', [$invited, $total]));
		}
		if ($added) {
			system_message(elgg_echo('groups:invite:result:added', [$added, $total]));
		}
		if ($skipped) {
			system_message(elgg_echo('groups:invite:result:skipped', [$skipped, $total]));
		}
		if ($error) {
			register_error(elgg_echo('groups:invite:result:error', [$error, $total]));
		}

		return elgg_ok_response();
	}
}