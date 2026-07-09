<?php

namespace hypeJunction\Invite;

use Elgg\IntegrationTestCase;

/**
 * Behavioural regression guards for hypeinvite migration fixes that only surface
 * on a booted Elgg 7 (global-helper loading, renamed core lookups, relationship
 * direction, and the InviteService email/code logic).
 */
class MigrationRegressionTest extends IntegrationTestCase {

	public function up() {}

	public function down() {}

	public function getPluginID(): string {
		return 'hypeinvite';
	}

	/**
	 * bd20553 — require_once lib/functions.php at the top of elgg-plugin.php was
	 * clobbered by a render-fix port. These git-tracked global helpers must be
	 * defined after the plugin boots, otherwise every invite call fatals.
	 */
	public function testGlobalHelpersDefinedAfterBoot(): void {
		$this->assertTrue(function_exists('users_invite_create_user_invite'));
		$this->assertTrue(function_exists('users_invite_get_user_invite'));
		$this->assertTrue(function_exists('users_invite_get_registration_link'));
		$this->assertTrue(function_exists('groups_invite_create_group_invite'));
	}

	/**
	 * de0c2dd — get_user_by_email → elgg_get_user_by_email, which now returns a
	 * single ElggUser|null (not an array). Call sites were changed from $users[0]
	 * to $user; this pins the core contract they depend on.
	 */
	public function testElggGetUserByEmailReturnsSingleEntity(): void {
		$user = $this->createUser();

		$found = elgg_get_user_by_email($user->email);

		$this->assertInstanceOf(\ElggUser::class, $found);
		$this->assertSame($user->guid, $found->guid);
	}

	/**
	 * 2aad38b — add_entity_relationship($a, $rel, $b) → $entity->addRelationship($b, $rel)
	 * flips the argument order. Assert the relationship is created in the correct
	 * direction (invite → invited_by → user), not reversed.
	 */
	public function testInvitedByRelationshipDirection(): void {
		elgg_call(ELGG_IGNORE_ACCESS, function () {
			$user = $this->createUser();

			$invite = new Invite();
			$invite->owner_guid = $user->guid;
			$invite->container_guid = $user->guid;
			$invite->access_id = ACCESS_PRIVATE;
			$invite->save();

			$invite->addRelationship($user->guid, 'invited_by');

			$this->assertTrue($invite->hasRelationship($user->guid, 'invited_by'));
			$this->assertFalse($user->hasRelationship($invite->guid, 'invited_by'));

			$invite->delete();
			$user->delete();
		});
	}

	/**
	 * cf74906 + keyBehavior — createInvite (via `new Invite()`, not removed
	 * elgg_new_entity) is idempotent by email, owned by the site, public, and
	 * carries a generated 32-char invite code.
	 */
	public function testCreateInviteIsIdempotentAndSiteOwned(): void {
		$svc = new InviteService();
		$email = uniqid('inv_', true) . '@example.test';
		$site = elgg_get_site_entity();

		$first = $svc->createInvite($email);
		$second = $svc->createInvite($email);

		$this->assertInstanceOf(Invite::class, $first);
		$this->assertSame('user_invite', $first->getSubtype());
		$this->assertSame($site->guid, (int) $first->owner_guid);
		$this->assertSame(ACCESS_PUBLIC, (int) $first->access_id);
		$this->assertSame(32, strlen((string) ((array) $first->invite_codes)[0]));
		$this->assertSame($first->guid, $second->guid);

		elgg_call(ELGG_IGNORE_ACCESS, fn() => $first->delete());
	}

	/**
	 * InviteService::validateInviteCode matches a stored user_invite whose email
	 * AND invite_codes both match; a mismatched code returns false.
	 */
	public function testValidateInviteCodeMatchesStoredEntity(): void {
		$svc = new InviteService();
		$email = uniqid('inv_', true) . '@example.test';

		$invite = $svc->createInvite($email);
		$code = (string) ((array) $invite->invite_codes)[0];

		$this->assertTrue($svc->validateInviteCode($email, $code));
		$this->assertFalse($svc->validateInviteCode($email, 'definitely-not-the-code'));

		elgg_call(ELGG_IGNORE_ACCESS, fn() => $invite->delete());
	}

	/**
	 * InviteService::generateInviteCode returns a unique 32-char string
	 * (crypto->getRandomString(32), replacing removed generate_random_cleartext_password).
	 */
	public function testGenerateInviteCodeIsUnique32Char(): void {
		$svc = new InviteService();

		$a = $svc->generateInviteCode();
		$b = $svc->generateInviteCode();

		$this->assertSame(32, strlen($a));
		$this->assertSame(32, strlen($b));
		$this->assertNotSame($a, $b);
	}
}
