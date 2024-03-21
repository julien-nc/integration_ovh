<?php

namespace OCA\OvhAi\Tests;

use OCA\OvhAi\AppInfo\Application;
use Test\TestCase;

class OvhAiAPIServiceTest extends TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('integration_ovhai', $app::APP_ID);
	}
}
