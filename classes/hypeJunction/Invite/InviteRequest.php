<?php

namespace hypeJunction\Invite;

use ElggObject;

/**
 * @property string $email
 * @property string $name
 * @property string $message
 */
class InviteRequest extends ElggObject {

	const SUBTYPE = 'user_invite_request';

	/**
	 * Initialize object attributes
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = self::SUBTYPE;
	}

}
