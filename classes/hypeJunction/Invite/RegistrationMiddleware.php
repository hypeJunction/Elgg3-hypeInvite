<?php

namespace hypeJunction\Invite;

use Elgg\HttpException;
use Elgg\Request;
use Exception;

class RegistrationMiddleware {

	/**
	 * Validate required invitation code
	 *
	 * @param Request $request
	 *
	 * @return void
	 * @throws HttpException
	 * @throws Exception
	 */
	public function __invoke(Request $request) {

		if (!elgg_get_plugin_setting('invite_only_network', 'hypeInvite')) {
			return;
		}

		$email = $request->getParam('email');
		$code = $request->getParam('invitation_code');

		if (empty($email) || empty($code)) {
			if (elgg_get_plugin_setting('request_invitation', 'hypeInvite')) {
				$redirect_url = elgg_generate_url('invite:request');
				$exception = new HttpException(elgg_echo('users:invite:invitation_code:empty'), ELGG_HTTP_FORBIDDEN);
				$exception->setRedirectUrl($redirect_url);
				throw $exception;
			}
		}

		$svc = elgg()->{'users.invites'};
		/* @var $svc \hypeJunction\Invite\InviteService */

		if (!$svc->validateInviteCode($email, $code)) {
			elgg_make_sticky_form('register');
			throw new HttpException(elgg_echo('users:invite:invitation_code:mismatch'), ELGG_HTTP_BAD_REQUEST);
		}
	}
}