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
 * @property BackendErrorController $BackendError
 */

class BackendErrorControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.language', 'plugin.core.setting');

	public function setUp() {
		$this->BackendError = new BackendErrorController();
		$this->BackendError->constructClasses();
		$this->BackendError->request = new CakeRequest();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->BackendError);

		parent::tearDown();
	}

	public function testBeforeFilter() {
		$this->_loginUser();
		$this->BackendError->request->params = Router::parse('/backend/users');
		$this->BackendError->request->url = 'backend/users';
		$this->BackendError->beforeFilter();
		$this->assertEqual('Core.default', $this->BackendError->layout);
	}

}
