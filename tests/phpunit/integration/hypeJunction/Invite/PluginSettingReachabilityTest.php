<?php

namespace hypeJunction\Invite;

use Elgg\IntegrationTestCase;

/**
 * Behavioural regression guard for the CRITICAL latent bug that every hypeinvite
 * plugin-setting is read with the camelCase id 'hypeInvite'.
 *
 * On Elgg 4.x+ the plugin id is the (lowercase) directory name 'hypeinvite'.
 * `\Elgg\Database\Plugins::get('hypeInvite')` does a case-sensitive lookup, so
 * `elgg_get_plugin_setting($name, 'hypeInvite', $default)` never finds the plugin
 * and silently returns the *default* — it does not read the stored value. That
 * makes invite_only_network, request_invitation, invite_friends,
 * friends/groups_accept_on_register, invitation_codes, invite_groups,
 * invite_code_register_form, groups_*_tab, groups_require_confirmation etc. all
 * dead on 7.x: the admin can set them but the plugin never sees them.
 *
 * This is proven empirically against a booted Elgg 7 (not just a source scan):
 *   - testCamelCaseIdSilentlyReturnsDefault pins the core 7.x contract that
 *     makes the migration necessary (WHY it breaks).
 *   - testEverySettingCallsiteResolvesToCanonicalPlugin walks the ACTUAL id
 *     literal at each source callsite and asserts the stored value flows through
 *     it. It is RED while the source passes 'hypeInvite' and flips GREEN the
 *     moment the callsites are lowercased to 'hypeinvite' — the RED→GREEN proof
 *     the migration fix landed.
 */
class PluginSettingReachabilityTest extends IntegrationTestCase {

	public function up() {}

	public function down() {}

	public function getPluginID(): string {
		return 'hypeinvite';
	}

	private function pluginRoot(): string {
		// .../tests/phpunit/integration/hypeJunction/Invite → plugin root is 5 levels up
		return dirname(__DIR__, 5);
	}

	/**
	 * Pins the Elgg 7.x contract this whole class exists for: a value stored on
	 * the canonical (lowercase) plugin id is readable via the lowercase id but a
	 * camelCase id resolves to no plugin, so elgg_get_plugin_setting returns the
	 * caller's default instead of the stored value.
	 */
	public function testCamelCaseIdSilentlyReturnsDefault(): void {
		$plugin = elgg_get_plugin_from_id('hypeinvite');
		$this->assertInstanceOf(\ElggPlugin::class, $plugin);

		// camelCase id must NOT resolve to a plugin on 4.x+ (root cause).
		$this->assertNull(elgg_get_plugin_from_id('hypeInvite'));

		$name = '__reachability_probe';
		$stored = 'STORED_' . uniqid('', true);
		$default = 'DEFAULT_SENTINEL';

		$this->assertTrue($plugin->setSetting($name, $stored));
		try {
			$this->assertSame(
				$stored,
				elgg_get_plugin_setting($name, 'hypeinvite', $default),
				'lowercase id must read the stored value'
			);
			$this->assertSame(
				$default,
				elgg_get_plugin_setting($name, 'hypeInvite', $default),
				'camelCase id must silently fall back to the default on Elgg 7.x'
			);
		} finally {
			$plugin->unsetSetting($name);
		}
	}

	/**
	 * The regression guard proper. Every plugin-setting/from-id callsite in the
	 * hypeinvite source whose id is a case-variant of 'hypeinvite' must read the
	 * value actually stored on the plugin. Uses the id literal EXACTLY as source
	 * passes it, so it fails until the callsites are lowercased.
	 */
	public function testEverySettingCallsiteResolvesToCanonicalPlugin(): void {
		$plugin = elgg_get_plugin_from_id('hypeinvite');
		$this->assertInstanceOf(\ElggPlugin::class, $plugin);

		$callsites = $this->hypeInviteSettingCallsites();
		// Scanner sanity: the plugin really does read its settings from source.
		$this->assertNotEmpty(
			$callsites,
			'expected hypeinvite plugin-setting callsites in source'
		);

		$name = '__reachability_probe';
		$stored = 'STORED_' . uniqid('', true);
		$default = 'DEFAULT_SENTINEL';

		// Group locations by the raw id literal so the failure message points at
		// every offending callsite.
		$byId = [];
		foreach ($callsites as $c) {
			$byId[$c['id']][] = $c['file'] . ':' . $c['line'];
		}

		$this->assertTrue($plugin->setSetting($name, $stored));
		$failures = [];
		try {
			foreach ($byId as $id => $locations) {
				$got = elgg_get_plugin_setting($name, $id, $default);
				if ($got !== $stored) {
					$failures[] = sprintf(
						"id '%s' → elgg_get_plugin_setting returned %s (settings unreachable; lowercase to 'hypeinvite'). Callsites:\n    %s",
						$id,
						var_export($got, true),
						implode("\n    ", $locations)
					);
				}
			}
		} finally {
			$plugin->unsetSetting($name);
		}

		$this->assertSame(
			[],
			$failures,
			"camelCase plugin-id callsites read the default, not the stored value, on Elgg 7.x:\n" . implode("\n", $failures)
		);
	}

	/**
	 * Scan plugin source (classes/ + views/, minus tests/vendor) for
	 * elgg_get_plugin_setting()/elgg_get_plugin_from_id() callsites whose id
	 * literal is a case-variant of the real plugin id 'hypeinvite'.
	 *
	 * @return list<array{file:string,line:int,id:string,setting:?string}>
	 */
	private function hypeInviteSettingCallsites(): array {
		$root = $this->pluginRoot();
		$out = [];
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
		);
		foreach ($it as $f) {
			$path = $f->getPathname();
			if (!str_ends_with($path, '.php')) {
				continue;
			}
			if (preg_match('#/(tests|vendor|vendors|node_modules)/#', $path)) {
				continue;
			}
			$rel = ltrim(str_replace($root, '', $path), '/');
			$lines = explode("\n", (string) file_get_contents($path));
			foreach ($lines as $i => $line) {
				if (preg_match_all(
					'/elgg_get_plugin_setting\s*\(\s*[\'"]([A-Za-z0-9_]+)[\'"]\s*,\s*[\'"]([A-Za-z0-9_]+)[\'"]/',
					$line,
					$m,
					PREG_SET_ORDER
				)) {
					foreach ($m as $hit) {
						if (strtolower($hit[2]) === 'hypeinvite') {
							$out[] = ['file' => $rel, 'line' => $i + 1, 'id' => $hit[2], 'setting' => $hit[1]];
						}
					}
				}
				if (preg_match_all(
					'/elgg_get_plugin_from_id\s*\(\s*[\'"]([A-Za-z0-9_]+)[\'"]/',
					$line,
					$m2,
					PREG_SET_ORDER
				)) {
					foreach ($m2 as $hit) {
						if (strtolower($hit[1]) === 'hypeinvite') {
							$out[] = ['file' => $rel, 'line' => $i + 1, 'id' => $hit[1], 'setting' => null];
						}
					}
				}
			}
		}
		return $out;
	}
}
