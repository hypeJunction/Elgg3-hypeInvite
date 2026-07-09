<?php

namespace hypeJunction\Invite;

use Elgg\UnitTestCase;

/**
 * Static, DB-free regression guards for the hypeinvite 6.x/7.x migration fixes
 * that live in source/config rather than runtime behaviour.
 *
 * Each test pins one migrationFix ref so a future edit that reintroduces the
 * pre-migration shape fails here instead of silently at page render on 7.x.
 */
class MigrationFixesTest extends UnitTestCase {

	public function up() {}

	public function down() {}

	private function pluginRoot(): string {
		// .../tests/phpunit/unit/hypeJunction/Invite → plugin root is 5 levels up
		return dirname(__DIR__, 5);
	}

	/**
	 * 2c18901 — composer autoload was psr-0 and silently failed under the 7.x
	 * plugin autoloader; it must be psr-4 mapping the Invite namespace to classes/.
	 */
	public function testComposerUsesPsr4Autoload(): void {
		$composer = json_decode((string) file_get_contents($this->pluginRoot() . '/composer.json'), true);

		$this->assertArrayHasKey('psr-4', $composer['autoload'] ?? []);
		$this->assertArrayNotHasKey('psr-0', $composer['autoload'] ?? []);
		$this->assertSame(
			'classes/hypeJunction/Invite/',
			$composer['autoload']['psr-4']['hypeJunction\\Invite\\'] ?? null
		);
	}

	/**
	 * 56576f1 — AMD modules were converted to ES modules for Elgg 6.x. The .mjs
	 * files must use ESM `import` syntax and must NOT contain a legacy AMD
	 * `define(` wrapper (which would never execute under the ESM importmap).
	 */
	public function testJsModulesUseEsmImports(): void {
		$modules = [
			'/views/default/object/user_invite_request/actions.mjs',
			'/views/default/admin/users/requests.mjs',
		];

		foreach ($modules as $rel) {
			$src = (string) file_get_contents($this->pluginRoot() . $rel);
			$this->assertMatchesRegularExpression('/^\s*import\s/m', $src, "$rel must use ESM import");
			$this->assertDoesNotMatchRegularExpression('/(^|[^\w])define\s*\(/', $src, "$rel must not use AMD define()");
		}
	}

	/**
	 * bc552f1 — get_user_by_username was removed in 5.x. The friends/invite
	 * resource must resolve the page owner via elgg_get_user_by_username and must
	 * not retain a bare get_user_by_username() call (fatal on 7.x).
	 */
	public function testFriendsInviteResourceUsesRenamedUserLookup(): void {
		$src = (string) file_get_contents($this->pluginRoot() . '/views/default/resources/friends/invite.php');

		$this->assertStringContainsString('elgg_get_user_by_username(', $src);
		$this->assertDoesNotMatchRegularExpression('/(?<![\w>])get_user_by_username\s*\(/', $src);
	}

	/**
	 * 0d5bffe / bb97298 — Seeder is a Seed subclass keyed to the LOWERCASE plugin
	 * id and seeds object/user_invite entities tagged with __faker (so unseed can
	 * find them). getType drives seed/unseed targeting; a wrong value orphans data.
	 */
	public function testSeederTypeAndCountOptions(): void {
		$this->assertSame('hypeinvite', Seeder::getType());

		$ref = new \ReflectionMethod(Seeder::class, 'getCountOptions');
		$ref->setAccessible(true);
		$opts = $ref->invoke((new \ReflectionClass(Seeder::class))->newInstanceWithoutConstructor());

		$this->assertSame('object', $opts['types']);
		$this->assertSame(Invite::SUBTYPE, $opts['subtypes']);
		$this->assertSame('user_invite', $opts['subtypes']);
		$this->assertSame('__faker', $opts['metadata_names']);
	}
}
