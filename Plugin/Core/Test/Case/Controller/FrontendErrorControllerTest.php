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

App::uses('FrontendErrorController', 'Core.Controller');
App::uses('CoreControllerTest', 'Core.Test/TestSuite');

/**
 * @property FrontendErrorController $FrontendError
 */

class FrontendErrorControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.language', 'plugin.core.core_setting');

	public function setUp() {
		$this->FrontendError = new FrontendErrorController();
		$this->FrontendError->constructClasses();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->FrontendError);

		parent::tearDown();
	}

	public function testBeforeFilter() {
		$this->FrontendError->beforeFilter();
		$this->assertEqual('error', $this->FrontendError->layout);
		$this->assertEqual('Errors', $this->FrontendError->viewPath);
	}

}
