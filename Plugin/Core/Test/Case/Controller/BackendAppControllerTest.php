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
 * @property BackendAppController $BackendApp
 */

class BackendAppControllerTest extends ControllerTestCase {

	public function setUp() {
		$this->BackendApp = new BackendAppController();
		$this->BackendApp->constructClasses();
	}

	public function testRequiredComponentsAreLoaded() {
		$this->assertNull($this->BackendApp->Components->RequestHandler);
		$this->BackendApp->beforeFilter();
		$this->assertEqual('RequestHandlerComponent', get_class($this->BackendApp->Components->RequestHandler));
	}

	public function testRequiredHelpersAreLoaded() {
		$this->assertEmpty($this->BackendApp->helpers);
		$this->BackendApp->beforeFilter();

		$expected = array(
			'Form',
			'Html',
			'Session'
		);

		$this->assertEqual($expected, $this->BackendApp->helpers);
	}

	public function tearDown() {
		unset($this->BackendApp);
		ClassRegistry::flush();
	}

}