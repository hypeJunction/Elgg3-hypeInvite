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
	public static function getType(): string {
		return 'hypeinvite';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getCountOptions(): array {
		return [
			'types' => 'object',
			'subtypes' => Invite::SUBTYPE,
			'metadata_names' => '__faker',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function seed() {
		while ($this->getCount() < $this->limit) {
			$owner = $this->getRandomUser() ?: $this->createUser();

			$invite = $this->createObject([
				'subtype' => Invite::SUBTYPE,
				'owner_guid' => $owner->guid,
				'container_guid' => $owner->guid,
				'access_id' => ACCESS_PRIVATE,
				'title' => $this->faker()->sentence(3),
				'email' => $this->faker()->safeEmail(),
			]);

			if (!$invite) {
				continue;
			}

			$this->advance();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function unseed() {
		$entities = \elgg_get_entities([
			'type' => 'object',
			'subtype' => Invite::SUBTYPE,
			'metadata_names' => '__faker',
			'limit' => 0,
			'batch' => true,
		]);
		/* @var $entities \ElggBatch */
		$entities->setIncrementOffset(false);

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
