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

App::uses('CoreControllerTest', 'Core.Test/TestSuite');
App::uses('CoreSettingsController', 'Core.Controller');

class CoreSettingsTestController extends CoreSettingsController {

	public $redirectUrl;

	public $renderView;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function render($view = null, $layout = null) {
		$this->renderView = $view;
	}

}

/**
 * @property CoreSettingsTestController $CoreSettings
 */

class CoreSettingsControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.setting', 'plugin.core.route', 'plugin.core.language');

	public function setUp() {
		$this->CoreSettings = $this->generate('CoreSettingsTest');
		$this->_loginUser();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->CoreSettings);

		parent::tearDown();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.CoreSetting', $this->CoreSettings->uses));
	}

	public function testEditActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/settings/edit', array('method' => 'get'));

		$this->assertInternalType('string', $this->CoreSettings->viewVars['title_for_layout']);
		$this->assertNull($this->CoreSettings->redirectUrl);

		$expected = $this->CoreSettings->CoreSetting->find('keyValues');
		$result = $this->CoreSettings->request->data;
		$this->assertEqual($expected, $result);
	}

	public function testEditActionPost() {
		$csCount = $this->CoreSettings->CoreSetting->find('count');

		$this->testAction('/' . $this->backendPrefix . '/settings/edit', array(
			'method' => 'post',
			'data' => array(
				'CoreSetting' => array(
					'application_name' => 'TestApp modified'
				)
			)
		));

		$this->assertEmpty($this->CoreSettings->CoreSetting->validationErrors);
		$this->assertEqual($csCount, $this->CoreSettings->CoreSetting->find('count'));
		$this->assertTrue($this->CoreSettings->CoreSetting->hasAny(array(
			'key' => 'application_name',
			'value' => 'TestApp modified'
		)));
		$this->assertEqual('success', $this->CoreSettings->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'edit'), $this->CoreSettings->redirectUrl);
	}

	public function testEditActionPostValidationError() {
		$csCount = $this->CoreSettings->CoreSetting->find('count');

		$this->testAction('/' . $this->backendPrefix . '/settings/edit', array(
			'method' => 'post',
			'data' => array(
				'CoreSetting' => array(
					'application_name' => ''
				)
			)
		));

		$this->assertNotEmpty($this->CoreSettings->CoreSetting->validationErrors);
		$this->assertEqual($csCount, $this->CoreSettings->CoreSetting->find('count'));
		$this->assertTrue($this->CoreSettings->CoreSetting->hasAny(array(
			'key' => 'application_name',
			'value' => 'TestApp'
		)));
		$this->assertEqual('error', $this->CoreSettings->Session->read('Message.flash.params.class'));
		$this->assertNull($this->CoreSettings->redirectUrl);
	}

}
