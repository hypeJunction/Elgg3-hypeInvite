<?php

namespace hypeJunction\Invite;

use Elgg\Database\Seeds\Seed;

/**
 * Seeds fake user invite objects for development and testing.
 */
class Seeder extends Seed {

	/**
	 * {@inheritdoc}
	 */
	public function seed() {
		$this->advance($this->getCount());

		while ($this->seedsCount() < $this->getCount()) {
			$owner = $this->getRandomUser();

			if (!$owner) {
				break;
			}

			$invite = new Invite();
			$invite->owner_guid = $owner->guid;
			$invite->container_guid = $owner->guid;
			$invite->access_id = ACCESS_PRIVATE;
			$invite->email = $this->faker->safeEmail();
			$invite->title = $this->faker->sentence(3);

			if (!$invite->save()) {
				continue;
			}

			$this->advance();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function unseed() {
		$entities = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'user_invite',
			'metadata_name' => '__faker',
			'limit' => false,
			'batch' => true,
		]);

		foreach ($entities as $entity) {
			$entity->delete();
			$this->advance();
		}
	}

	/**
	 * Registers this seeder with the seeds:database event.
	 *
	 * @param \Elgg\Event $event seeds:database event
	 * @return array
	 */
	public static function addSeed(\Elgg\Event $event) {
		$seeds = $event->getValue();
		$seeds[] = self::class;
		return $seeds;
	}
}
