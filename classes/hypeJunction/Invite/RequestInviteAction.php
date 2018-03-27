<?php

namespace hypeJunction\Invite;

use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class RequestInviteAction {

	/**
	 * Request an invitation
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws \Exception
	 */
	public function __invoke(Request $request) {

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($request) {
			$site = elgg_get_site_entity();

			$invite_request = new InviteRequest();
			$invite_request->owner_guid = $site->guid;
			$invite_request->container_guid = $site->guid;
			$invite_request->access_id = ACCESS_PUBLIC;
			$invite_request->name = $request->getParam('name');
			$invite_request->email = $request->getParam('email');
			$invite_request->message = $request->getParam('message');

			$invite_request->save();

			$to = elgg_get_admins([
				'callback' => function($e) {
					return $e->guid;
				}
			]);

			$subject = elgg_echo('users:invite:request:notify:subject');
			$message = elgg_echo('users:invite:request:notify:message', [
				$invite_request->name,
				$invite_request->email,
				$invite_request->message,
				elgg_normalize_url('admin/users/requests'),
			]);

			notify_user($to, null, $subject, $message, [
				'action' => 'create',
				'object' => $invite_request,
				'url' => elgg_normalize_url('admin/users/requests'),
			]);

			return elgg_ok_response([
				'entity' => $invite_request,
			], elgg_echo('users:invite:request:success'), '');
		});
	}
}