<?php

namespace hypeJunction\Invite;

use Elgg\Event;

/** Removes group invite item from title menu (shown in entity menu). */
class GroupTitleMenu {

	/**
	 * Setup group title menu
	 *
	 * @param Event $event Event
	 *
	 * @return \ElggMenuItem[]
	 */
	public function __invoke(Event $event) {

		$menu = $event->getValue();
		$group = $event->getEntityParam();

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
