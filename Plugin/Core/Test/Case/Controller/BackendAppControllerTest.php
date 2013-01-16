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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('BackendAppController', 'Core.Controller');
App::uses('ControllerTestCase', 'TestSuite');

/**
 * @property BackendAppController $BackendAppController
 */

class BackendAppControllerTest extends ControllerTestCase {

	public function setUp() {
		$this->BackendAppController = new BackendAppController();
		$this->BackendAppController->constructClasses();
	}

	public function testRequiredComponentsAreLoaded() {
		$this->assertTrue(array_key_exists('RequestHandler', $this->BackendAppController->components));
		$this->assertTrue(array_key_exists('Core.Authenticator', $this->BackendAppController->components));
	}

	public function testRequiredHelpersAreLoaded() {
		$this->assertTrue(in_array('Form', $this->BackendAppController->helpers));
		$this->assertTrue(in_array('Html', $this->BackendAppController->helpers));
		$this->assertTrue(in_array('Session', $this->BackendAppController->helpers));
	}

	public function tearDown() {
		unset($this->BackendApp);
		ClassRegistry::flush();
	}

}
