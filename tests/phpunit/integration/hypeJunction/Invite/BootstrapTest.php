<?php

namespace hypeJunction\Invite;

use Elgg\IntegrationTestCase;

/**
 * Characterization suite for hypeinvite on Elgg 4.x.
 *
 * Locks in plugin lifecycle, entity subtype mapping (3 subtypes sharing
 * 2 classes), action registration, and declarative hook/event wiring from
 * elgg-plugin.php so future changes can't silently regress any of them.
 */
class BootstrapTest extends IntegrationTestCase {

	public function getPluginID(): string {
		return 'hypeinvite';
	}

	public function up() {}
	public function down() {}

	// --- plugin lifecycle ---

	public function testPluginIsRegistered() {
		$plugin = elgg_get_plugin_from_id('hypeinvite');
		$this->assertInstanceOf(\ElggPlugin::class, $plugin);
	}

	public function testPluginIsEnabled() {
		$this->assertTrue(elgg_get_plugin_from_id('hypeinvite')->isEnabled());
	}

	public function testPluginIsActive() {
		$this->assertTrue(elgg_get_plugin_from_id('hypeinvite')->isActive());
	}

	// --- class autoloading ---

	public function testBootstrapClassLoads() {
		$this->assertTrue(class_exists(Bootstrap::class));
	}

	public function testInviteClassLoads() {
		$this->assertTrue(class_exists(Invite::class));
	}

	public function testInviteRequestClassLoads() {
		$this->assertTrue(class_exists(InviteRequest::class));
	}

	public function testInviteExtendsElggObject() {
		$r = new \ReflectionClass(Invite::class);
		$this->assertTrue($r->isSubclassOf(\ElggObject::class));
	}

	public function testInviteRequestExtendsElggObject() {
		$r = new \ReflectionClass(InviteRequest::class);
		$this->assertTrue($r->isSubclassOf(\ElggObject::class));
	}

	// --- entity subtype mappings (3 subtypes, 2 classes) ---

	public function testUserInviteSubtypeMappedToInvite() {
		$this->assertSame(Invite::class, elgg_get_entity_class('object', 'user_invite'));
	}

	public function testGroupInviteSubtypeMappedToInvite() {
		$this->assertSame(Invite::class, elgg_get_entity_class('object', 'group_invite'));
	}

	public function testUserInviteRequestSubtypeMappedToInviteRequest() {
		$this->assertSame(InviteRequest::class, elgg_get_entity_class('object', 'user_invite_request'));
	}

	public function testInviteSubtypeConstant() {
		// The class's own constant defaults to user_invite; elgg-plugin.php
		// maps both user_invite and group_invite to this class and the
		// caller sets the subtype attribute to discriminate.
		$this->assertSame('user_invite', Invite::SUBTYPE);
	}

	public function testInviteRequestSubtypeConstant() {
		$this->assertSame('user_invite_request', InviteRequest::SUBTYPE);
	}

	// --- actions ---

	public function testPublicRequestInvitationActionRegistered() {
		$this->assertTrue(_elgg_services()->actions->exists('users/request_invitation'));
	}

	public function testRegularInviteUsersActionRegistered() {
		$this->assertTrue(_elgg_services()->actions->exists('users/invite'));
	}

	public function testRegularInviteGroupMembersActionRegistered() {
		$this->assertTrue(_elgg_services()->actions->exists('groups/invite'));
	}

	public function testAdminOnlyConfirmInvitationIsRegisteredByController() {
		// elgg-plugin.php declares 'users/confirm_invitation' with both
		// a 'controller' callable and access='admin'. Unlike the legacy
		// file-path-based admin actions (which Elgg filters in stateless
		// contexts — see hypeinbox's BootstrapTest), the declarative
		// controller form registers regardless of session, and access
		// control runs at dispatch instead of registration. Pin the
		// declarative-controller behavior.
		$this->assertTrue(_elgg_services()->actions->exists('users/confirm_invitation'));
	}

	// --- declarative hook wiring (elgg-plugin.php 'hooks' block) ---

	public function testRegistrationUrlHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('registration_url', $handlers);
		$this->assertArrayHasKey('site', $handlers['registration_url']);
	}

	public function testAcceptInviteHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('accept', $handlers);
		$this->assertArrayHasKey('invite', $handlers['accept']);
	}

	public function testRegisterUserHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('register', $handlers);
		$this->assertArrayHasKey('user', $handlers['register']);
	}

	public function testPageMenuHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('register', $handlers);
		$this->assertArrayHasKey('menu:page', $handlers['register']);
	}

	public function testEntityMenuHookWired() {
		$handlers = _elgg_services()->hooks->getAllHandlers();
		$this->assertArrayHasKey('register', $handlers);
		$this->assertArrayHasKey('menu:entity', $handlers['register']);
	}

	// --- declarative event wiring ---

	public function testCreateUserEventWired() {
		$events = _elgg_services()->events->getAllHandlers();
		$this->assertArrayHasKey('create', $events);
		$this->assertArrayHasKey('user', $events['create']);
	}
}
