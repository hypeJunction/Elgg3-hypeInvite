<?php

namespace hypeJunction\Invite;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {

	public function init() {
		if (\elgg_is_active_plugin('hypeHero')) {
			\elgg_register_plugin_hook_handler('register', 'menu:title', GroupTitleMenu::class, 800);
			\elgg_register_plugin_hook_handler('register', 'menu:actions', GroupTitleMenu::class, 800);
		}

		\elgg()->group_tools->register('invites', [
			'default_on' => false,
		]);
	}
}
