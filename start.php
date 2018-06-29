<?php

/**
 * hypeInvite
 *
 * An interface for inviting new users to the site
 * 
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016-2018, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

return function() {
	elgg_register_event_handler('init', 'system', function () {

		elgg_register_plugin_hook_handler('registration_url', 'site', \hypeJunction\Invite\GenerateRegistrationUrl::class);
		elgg_register_plugin_hook_handler('register', 'user', \hypeJunction\Invite\RegistrationForwardUrl::class, 1000);

		elgg_register_plugin_hook_handler('register', 'menu:page', \hypeJunction\Invite\PageMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:entity', \hypeJunction\Invite\EntityMenu::class);

		if (elgg_is_active_plugin('hypeHero')) {
			elgg_register_plugin_hook_handler('register', 'menu:title', \hypeJunction\Invite\GroupTitleMenu::class, 800);
			elgg_register_plugin_hook_handler('register', 'menu:actions', \hypeJunction\Invite\GroupTitleMenu::class, 800);
		}

		elgg_register_event_handler('create', 'user', \hypeJunction\Invite\ProcessUserInvitesOnRegistration::class);
		elgg_register_plugin_hook_handler('accept', 'invite', \hypeJunction\Invite\AcceptFriendInvitesOnRegistration::class);
		elgg_register_plugin_hook_handler('accept', 'invite', \hypeJunction\Invite\AcceptGroupInvitesOnRegistration::class);

		elgg_extend_view('register/extend', 'forms/register/invitation_code', 100);

		elgg()->group_tools->register('invites', [
			'default_on' => false,
		]);

	});
};

