<?php

namespace hypeJunction\Invite;

use Elgg\DefaultPluginBootstrap;

/** Plugin bootstrap. */
class Bootstrap extends DefaultPluginBootstrap {

	/** {@inheritdoc} */
	public function init() {
		if (\elgg_is_active_plugin('hypeHero')) {
			\elgg_register_event_handler('register', 'menu:title', GroupTitleMenu::class, 800);
			\elgg_register_event_handler('register', 'menu:actions', GroupTitleMenu::class, 800);
		}

		// group_tools 'invites' is now registered declaratively in elgg-plugin.php
	}
}
