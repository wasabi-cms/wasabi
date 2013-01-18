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

App::uses('ControllerTestCase', 'TestSuite');
App::uses('LanguagesController', 'Core.Controller');

class LanguagesTestController extends LanguagesController {

	public $redirectUrl;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return false;
	}

}

/**
 * @property LanguagesTestController $LanguagesController
 */

class LanguagesControllerTest extends ControllerTestCase {

	public $fixtures = array('plugin.core.language');

	public function setUp() {
		$this->LanguagesController = new LanguagesTestController();
		$this->LanguagesController->constructClasses();
		$this->LanguagesController->Components->init($this->LanguagesController);

		parent::setUp();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.Language', $this->LanguagesController->uses));
	}

	public function testIndexAction() {
		$this->LanguagesController->index();
		$this->assertTrue(isset($this->LanguagesController->viewVars['languages']));

		$expected = array(
			array(
				'Language' => array(
					'id' => 1,
					'name' => 'English',
					'locale' => 'en',
					'iso' => 'eng',
					'lang' => 'en-US',
					'available_at_frontend' => true,
					'available_at_backend' => true,
					'position' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'Language' => array(
					'id' => 2,
					'name' => 'Deutsch',
					'locale' => 'de',
					'iso' => 'deu',
					'lang' => 'de-DE',
					'available_at_frontend' => false,
					'available_at_backend' => true,
					'position' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$result = $this->LanguagesController->viewVars['languages'];
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		unset($this->LanguagesController);

		parent::tearDown();
	}

}
