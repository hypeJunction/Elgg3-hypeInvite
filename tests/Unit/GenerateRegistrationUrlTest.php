<?php

namespace hypeJunction\Invite\Tests\Unit;

use hypeJunction\Invite\GenerateRegistrationUrl;
use PHPUnit\Framework\TestCase;

/**
 * Basic smoke test ensuring the class can be autoloaded.
 * Functional behaviour requires a running Elgg instance and is covered
 * by the Integration suite.
 *
 * @group unit
 */
class GenerateRegistrationUrlTest extends TestCase {

    public function testClassExists() {
        $this->assertTrue(class_exists(GenerateRegistrationUrl::class));
    }
}
