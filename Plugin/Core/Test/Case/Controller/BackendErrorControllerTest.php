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

App::uses('BackendErrorController', 'Core.Controller');
App::uses('CoreControllerTest', 'Core.Test/TestSuite');

/**
 * @property BackendErrorController $BackendErrors
 */

class BackendErrorControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.language', 'plugin.core.core_setting');

	public function setUp() {
		$this->BackendErrors = new BackendErrorController();
		$this->BackendErrors->constructClasses();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->BackendErrors);

		parent::tearDown();
	}

	public function testBeforeFilter() {
		$this->_loginUser();
		$this->BackendErrors->request->params = Router::parse('/backend/users');
		$this->BackendErrors->request->url = 'backend/users';
		$this->BackendErrors->beforeFilter();
		$this->assertEqual('Core.backend_error', $this->BackendErrors->layout);
	}

}
