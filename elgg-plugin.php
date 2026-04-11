<?php

return [
	'bootstrap' => \hypeJunction\Invite\Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'user_invite',
			'class' => \hypeJunction\Invite\Invite::class,
			'searchable' => false,
		],
		[
			'type' => 'object',
			'subtype' => 'group_invite',
			'class' => \hypeJunction\Invite\Invite::class,
			'searchable' => false,
		],
		[
			'type' => 'object',
			'subtype' => 'user_invite_request',
			'class' => \hypeJunction\Invite\InviteRequest::class,
			'searchable' => false,
		],
	],
	'routes' => [
		'account:register' => [
			'path' => '/register',
			'resource' => 'account/register',
			'walled' => false,
			'middleware' => [
				\hypeJunction\Invite\RegistrationMiddleware::class,
			],
		],
		'friends:invite' => [
			'path' => '/friends/{username?}/invite',
			'resource' => 'friends/invite',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
		'invite:request' => [
			'path' => '/users/request_invitation',
			'resource' => 'users/request_invitation',
			'walled' => false,
		],
		'invite:group:group:confirm' => [
			'path' => '/groups/confirm_invitation',
			'controller' => \hypeJunction\Invite\ConfirmGroupInvite::class,
			'walled' => false,
		],
	],
	'actions' => [
		'users/request_invitation' => [
			'controller' => \hypeJunction\Invite\RequestInviteAction::class,
			'access' => 'public',
		],
		'users/confirm_invitation' => [
			'controller' => \hypeJunction\Invite\ConfirmInviteAction::class,
			'access' => 'admin',
		],
		'users/invite' => [
			'controller' => \hypeJunction\Invite\InviteUsersAction::class,
		],
		'groups/invite' => [
			'controller' => \hypeJunction\Invite\InviteGroupMembersAction::class,
		],
	],
	'hooks' => [
		'registration_url' => [
			'site' => [
				\hypeJunction\Invite\GenerateRegistrationUrl::class => [],
			],
		],
		'register' => [
			'user' => [
				\hypeJunction\Invite\RegistrationForwardUrl::class => ['priority' => 1000],
			],
			'menu:page' => [
				\hypeJunction\Invite\PageMenu::class => [],
			],
			'menu:entity' => [
				\hypeJunction\Invite\EntityMenu::class => [],
			],
		],
		'accept' => [
			'invite' => [
				\hypeJunction\Invite\AcceptFriendInvitesOnRegistration::class => [],
				\hypeJunction\Invite\AcceptGroupInvitesOnRegistration::class => [],
			],
		],
	],
	'events' => [
		'create' => [
			'user' => [
				\hypeJunction\Invite\ProcessUserInvitesOnRegistration::class => [],
			],
		],
	],
	'view_extensions' => [
		'register/extend' => [
			'forms/register/invitation_code' => ['priority' => 100],
		],
	],
];