<?php

namespace hypeJunction\Invite;

use Elgg\Hook;

class EntityMenu {

	/**
	 * Setup group entity menu
	 *
	 * @param Hook $hook Hook
	 *
	 * @return \ElggMenuItem[]
	 */
	public function __invoke(Hook $hook) {

		$menu = $hook->getValue();
		$group = $hook->getEntityParam();

		if (!$group instanceof \ElggGroup) {
			return;
		}

		$user = elgg_get_logged_in_user_entity();

		if ($group->isMember($user) && ($group->owner_guid == $user->guid || $group->isToolEnabled('invites') || elgg_is_admin_logged_in())) {
			$menu[] = \ElggMenuItem::factory([
				'name' => 'groups:invite',
				'href' => elgg_generate_url("invite:group:$group->subtype", [
					'guid' => $group->guid,
				]),
				'text' => elgg_echo('groups:invite'),
				'icon' => 'user-plus',
				'priority' => 100,
			]);
		}

		return $menu;
	}
}