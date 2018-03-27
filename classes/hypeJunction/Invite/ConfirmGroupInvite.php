<?php

namespace hypeJunction\Invite;

use Elgg\CsrfException;
use Elgg\EntityNotFoundException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;
use Exception;

class ConfirmGroupInvite {

	/**
	 * Confirm group invite
	 *
	 * @param Request $request
	 *
	 * @return ResponseBuilder
	 * @throws CsrfException
	 * @throws Exception
	 */
	public function __invoke(Request $request) {

		$i = (int) $request->getParam('i');
		$g = (int) $request->getParam('g');

		$hmac = elgg_build_hmac([
			'i' => $i,
			'g' => $g,
		]);

		if (!$hmac->matchesToken($request->getParam('m'))) {
			throw new CsrfException();
		}

		return elgg_call(ELGG_IGNORE_ACCESS, function () use ($i, $g) {
			$user = get_entity($i);
			$group = get_entity($g);

			if (!$user instanceof \ElggUser || !$group instanceof \ElggGroup) {
				throw new EntityNotFoundException(elgg_echo('groups:invite:confirm:error'));
			}

			if ($group->join($user)) {
				return elgg_ok_response([
					'group' => $group,
					'user' => $user,
					], elgg_echo('groups:joined'), $group->getURL());
			}

			return elgg_error_response(elgg_echo('groups:invite:confirm:error'));
		});

	}
}