<?php

namespace hypeJunction\Invite;

use Elgg\Hook;

class GroupTitleMenu {

	/**
	 * Setup group title menu
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

		foreach ($menu as $key => $item) {
			if (in_array($item->getName(), ['groups:invite'])) {
				unset($menu[$key]);
				continue;
			}
		}

		return $menu;
	}
}