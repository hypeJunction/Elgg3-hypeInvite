<?php

namespace hypeJunction\Invite;

use Elgg\Hook;
use ElggMenuItem;

class PageMenu {

	/**
	 * Setup page menu
	 *
	 * @param Hook $hook Hook
	 *
	 * @return ElggMenuItem[]
	 */
	public function __invoke(Hook $hook) {

		$menu = $hook->getValue();

		if (elgg_get_plugin_setting('invite_friends', 'hypeInvite')) {
			$page_owner = elgg_get_page_owner_entity();

			if ($page_owner instanceof \ElggUser) {
				$menu[] = ElggMenuItem::factory([
					'name' => 'friends:invite',
					'href' => elgg_generate_url('friends:invite', [
						'username' => $page_owner->username,
					]),
						'text' => elgg_echo('users:invite:invite'),
					'context' => ['friends'],
					'section' => 'actions',
					'icon' => 'envelope-o',
				]);
			}
		}

		$menu[] = ElggMenuItem::factory([
			'name' => 'users:invite',
			'href' => 'admin/users/invite',
			'text' => elgg_echo('admin:users:invite'),
			'section' => 'administer',
			'parent_name' => 'users',
			'context' => ['admin'],
			'icon' => 'user-plus',
		]);

		$menu[] = ElggMenuItem::factory([
			'name' => 'users:invitations',
			'href' => 'admin/users/invitations',
			'text' => elgg_echo('admin:users:invitations'),
			'section' => 'administer',
			'parent_name' => 'users',
			'context' => ['admin'],
			'icon' => 'envelope-open-o',
		]);

		$menu[] = ElggMenuItem::factory([
			'name' => 'users:requests',
			'href' => 'admin/users/requests',
			'text' => elgg_echo('admin:users:requests'),
			'section' => 'administer',
			'parent_name' => 'users',
			'context' => ['admin'],
			'icon' => 'inbox',
		]);

		return $menu;
	}

}
