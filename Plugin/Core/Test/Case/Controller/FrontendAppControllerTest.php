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

App::uses('FrontendAppController', 'Core.Controller');
App::uses('CoreControllerTest', 'Core.Test/TestSuite');

/**
 * @property FrontendAppController $Frontend
 */

class FrontendAppControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.language', 'plugin.core.core_setting');

	public function setUp() {
		$this->Frontend = new FrontendAppController();
		$this->Frontend->constructClasses();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Frontend);

		parent::tearDown();
	}

	public function testRequiredComponentsAreLoaded() {
		$this->assertTrue(array_key_exists('RequestHandler', $this->Frontend->components));
		$this->assertFalse(array_key_exists('Core.Authenticator', $this->Frontend->components));
	}

	public function testRequiredHelpersAreLoaded() {
		$this->assertTrue(in_array('Form', $this->Frontend->helpers));
		$this->assertTrue(in_array('Html', $this->Frontend->helpers));
		$this->assertTrue(in_array('Session', $this->Frontend->helpers));
	}

	public function testViewClassIsCorrect() {
		$this->assertEqual('Core.Core', $this->Frontend->viewClass);
	}

	public function testLoadLanguages() {
		$this->Frontend->loadLanguages();

		$this->assertEqual('English', Configure::read('Wasabi.content_language.name'));
		$this->assertEqual('eng', Configure::read('Config.language'));

		/**
		 * @var Language $language_model
		 */
		$language_model = ClassRegistry::init('Core.Language');
		$language_model->id = 2;
		$language_model->saveField('available_at_frontend', true);

		$this->Frontend->loadLanguages(2);

		$this->assertEqual('Deutsch', Configure::read('Wasabi.content_language.name'));
		$this->assertEqual('deu', Configure::read('Config.language'));
	}

}