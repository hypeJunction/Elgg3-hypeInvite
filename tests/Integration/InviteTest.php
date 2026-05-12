<?php

namespace hypeJunction\Invite\Tests\Integration;

use Elgg\IntegrationTestCase;
use hypeJunction\Invite\Invite;
use hypeJunction\Invite\InviteRequest;

/**
 * @group integration
 */
class InviteTest extends IntegrationTestCase {

    public function testInviteSubtype() {
        $invite = new Invite();
        $this->assertEquals('user_invite', $invite->getSubtype());
    }

    public function testInviteRequestSubtype() {
        $request = new InviteRequest();
        $this->assertEquals('user_invite_request', $request->getSubtype());
    }
}
