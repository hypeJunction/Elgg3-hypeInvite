<?php

namespace hypeJunction\Invite;

use Elgg\IntegrationTestCase;

/**
 * Lock in Invite + InviteRequest entity CRUD on Elgg 4.x.
 *
 * Both Invite subtypes (user_invite, group_invite) share a single class
 * that hardcodes SUBTYPE = 'user_invite' in initializeAttributes. Callers
 * that want a group_invite must override the subtype attribute after
 * construction. The tests pin both the default (user_invite) and the
 * override path.
 */
class EntityCrudTest extends IntegrationTestCase {

	public function up() {}
	public function down() {}

	public function getPluginID(): string {
		return 'hypeinvite';
	}

	private function makeInvite(array $overrides = []): Invite {
		return \elgg_call(ELGG_IGNORE_ACCESS, function () use ($overrides) {
			$user = $overrides['__user'] ?? $this->createUser();
			$i = new Invite();
			if (isset($overrides['subtype'])) {
				$i->attributes['subtype'] = $overrides['subtype'];
			}
			$i->owner_guid = $overrides['owner_guid'] ?? $user->guid;
			$i->container_guid = $overrides['container_guid'] ?? $user->guid;
			$i->access_id = $overrides['access_id'] ?? ACCESS_PRIVATE;
			if (isset($overrides['title'])) {
				$i->title = $overrides['title'];
			}
			if (isset($overrides['description'])) {
				$i->description = $overrides['description'];
			}
			$i->save();
			return $i;
		});
	}

	private function makeInviteRequest(array $overrides = []): InviteRequest {
		return \elgg_call(ELGG_IGNORE_ACCESS, function () use ($overrides) {
			$user = $overrides['__user'] ?? $this->createUser();
			$r = new InviteRequest();
			$r->owner_guid = $overrides['owner_guid'] ?? $user->guid;
			$r->container_guid = $overrides['container_guid'] ?? $user->guid;
			$r->access_id = $overrides['access_id'] ?? ACCESS_PRIVATE;
			if (isset($overrides['description'])) {
				$r->description = $overrides['description'];
			}
			$r->save();
			return $r;
		});
	}

	// --- Invite defaults ---

	public function testInviteInitializesAsUserInviteSubtype(): void {
		$i = new Invite();
		$this->assertSame('user_invite', $i->getSubtype());
	}

	public function testInviteRequestInitializesAsUserInviteRequestSubtype(): void {
		$r = new InviteRequest();
		$this->assertSame('user_invite_request', $r->getSubtype());
	}

	// --- Invite CRUD ---

	public function testCreatedUserInviteHasGuid(): void {
		$i = $this->makeInvite();
		$this->assertGreaterThan(0, $i->guid);
		$this->assertSame('object', $i->type);
		$this->assertSame('user_invite', $i->getSubtype());
		$i->delete();
	}

	public function testLoadedUserInviteIsInviteInstance(): void {
		$i = $this->makeInvite();
		$guid = $i->guid;
		\_elgg_services()->entityCache->delete($guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($guid));
		$this->assertInstanceOf(Invite::class, $loaded);
		$i->delete();
	}

	public function testOverridingSubtypeAttributeDoesNotPersistAsGroupInvite(): void {
		// Characterization: Invite::initializeAttributes hardcodes
		// attributes['subtype'] = 'user_invite'. Setting
		// attributes['subtype'] = 'group_invite' AFTER construction does
		// NOT round-trip through save/load: the loaded entity still has
		// subtype 'user_invite'. Callers that want a group_invite must
		// construct through a different code path (Invite has no factory
		// for it — elgg-plugin.php maps the subtype to the class, but
		// entity instantiation itself hardcodes user_invite). Pin the
		// current footgun so a future fix surfaces as a test update.
		$i = $this->makeInvite(['subtype' => 'group_invite']);
		$guid = $i->guid;
		\_elgg_services()->entityCache->delete($guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($guid));
		$this->assertInstanceOf(Invite::class, $loaded);
		$this->assertSame('user_invite', $loaded->getSubtype());
		$i->delete();
	}

	public function testInviteDescriptionPersists(): void {
		$i = $this->makeInvite(['description' => 'invite body text']);
		\_elgg_services()->entityCache->delete($i->guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($i->guid));
		$this->assertSame('invite body text', (string) $loaded->description);
		$i->delete();
	}

	public function testInviteTitlePersists(): void {
		$i = $this->makeInvite(['title' => 'invite title']);
		\_elgg_services()->entityCache->delete($i->guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($i->guid));
		$this->assertSame('invite title', (string) $loaded->title);
		$i->delete();
	}

	public function testInviteDeleteReturnsTruthy(): void {
		$i = $this->makeInvite();
		$result = \elgg_call(ELGG_IGNORE_ACCESS, fn() => $i->delete());
		$this->assertNotFalse($result);
	}

	// --- InviteRequest CRUD ---

	public function testCreatedInviteRequestHasGuid(): void {
		$r = $this->makeInviteRequest();
		$this->assertGreaterThan(0, $r->guid);
		$this->assertSame('object', $r->type);
		$this->assertSame('user_invite_request', $r->getSubtype());
		$r->delete();
	}

	public function testLoadedInviteRequestIsInviteRequestInstance(): void {
		$r = $this->makeInviteRequest();
		$guid = $r->guid;
		\_elgg_services()->entityCache->delete($guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($guid));
		$this->assertInstanceOf(InviteRequest::class, $loaded);
		$r->delete();
	}

	public function testInviteRequestDescriptionPersists(): void {
		$r = $this->makeInviteRequest(['description' => 'request reason']);
		\_elgg_services()->entityCache->delete($r->guid);
		$loaded = \elgg_call(ELGG_IGNORE_ACCESS, fn() => get_entity($r->guid));
		$this->assertSame('request reason', (string) $loaded->description);
		$r->delete();
	}
}
