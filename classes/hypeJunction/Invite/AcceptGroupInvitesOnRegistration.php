<?php
/**
 *
 */

namespace hypeJunction\Invite;

use Elgg\Event;
use Exception;

/** Create group memberships when a group invite is accepted. */
class AcceptGroupInvitesOnRegistration {

	/**
	 * Create group invites when the invite is accepted
	 *
	 * @elgg_event accept invite
	 *
	 * @param Event $event Event
	 *
	 * @return void
	 * @throws Exception
	 */
	public function __invoke(Event $event) {
		$return = $event->getValue();
		$params = $event->getParams();

		if ($return === false) {
			return;
		}

		$invite = \elgg_extract('invite', $params);
		$user = \elgg_extract('user', $params);

		\elgg_call(ELGG_IGNORE_ACCESS, function () use ($invite, $user) {

			$groups = new \ElggBatch('elgg_get_entities', [
				'types' => 'group',
				'relationship' => 'invited_to',
				'relationship_guid' => (int) $invite->guid,
				'inverse_relationship' => false,
				'limit' => 0,
			]);

			$ref = get_input('ref');

			$accept_on_register = \elgg_get_plugin_setting('groups_accept_on_register', 'hypeInvite');

			foreach ($groups as $group) {
				add_entity_relationship($group->guid, 'invited', $user->guid);

				if (is_callable('\AU\SubGroups\get_parent_group')) {
					// AU Subgroups is unable to resolve invites properly
					// unless we also invite the user to all parent groups
					$parent = $group;

					while ($parent = \AU\SubGroups\get_parent_group($parent)) {
						add_entity_relationship($parent->guid, 'invited', $user->guid);
					}
				}

				if ($accept_on_register || $ref == $group->guid) {
					if ($group->join($user)) {
						$subject = \elgg_echo('groups:welcome:subject', [$group->getDisplayName()], $user->language);

						$body = \elgg_echo('groups:welcome:body', [
							$user->getDisplayName(),
							$group->getDisplayName(),
							$group->getURL(),
						], $user->language);

						$params = [
							'action' => 'add_membership',
							'object' => $group,
							'url' => $group->getURL(),
						];

						notify_user($user->guid, $group->owner_guid, $subject, $body, $params);
					}
				}
			}
		});
	}
}
