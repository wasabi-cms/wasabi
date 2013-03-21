<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestCase', 'TestSuite');
App::uses('ClassRegistry', 'Utility');
App::uses('Route', 'Core.Model');

/**
 * @property Route $Route
 */

class RouteTest extends CakeTestCase {

	public $fixtures = array('plugin.core.route');

	public function setUp() {
		$this->Route = ClassRegistry::init('Core.Route');

		parent::setUp();
	}

	public function testNothingToTestRightNow() {

	}

	public function tearDown() {
		unset($this->Route);

		parent::tearDown();
	}

}