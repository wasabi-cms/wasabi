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
App::uses('CoreGeneralSetting', 'Core.Model');
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

	public function testGeneralActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/settings/general', array('method' => 'get'));

		$this->assertInternalType('string', $this->CoreSettings->viewVars['title_for_layout']);
		$this->assertNull($this->CoreSettings->redirectUrl);

		$expected = array(
			'CoreGeneralSetting' => array(
				'application_name' => 'TestApp'
			)
		);
		$result = $this->CoreSettings->request->data;
		$this->assertEqual($expected, $result);
	}

	public function testGeneralActionPost() {
		$CoreGeneralSetting = ClassRegistry::init('Core.CoreGeneralSetting');
		$csCount = $CoreGeneralSetting->find('count');

		$this->testAction('/' . $this->backendPrefix . '/settings/general', array(
			'method' => 'post',
			'data' => array(
				'CoreGeneralSetting' => array(
					'application_name' => 'TestApp modified'
				)
			)
		));

		$this->assertEmpty($this->CoreSettings->CoreGeneralSetting->validationErrors);
		$this->assertEqual($csCount, $this->CoreSettings->CoreGeneralSetting->find('count'));
		$this->assertTrue($this->CoreSettings->CoreGeneralSetting->hasAny(array(
			'key' => 'application_name',
			'value' => 'TestApp modified'
		)));
		$this->assertEqual('success', $this->CoreSettings->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'general'), $this->CoreSettings->redirectUrl);
	}

	public function testEditActionPostValidationError() {
		$this->testAction('/' . $this->backendPrefix . '/settings/general', array(
			'method' => 'post',
			'data' => array(
				'CoreGeneralSetting' => array(
					'application_name' => ''
				)
			)
		));

		$this->assertNotEmpty($this->CoreSettings->CoreGeneralSetting->validationErrors);
		$this->assertTrue($this->CoreSettings->CoreGeneralSetting->hasAny(array(
			'key' => 'application_name',
			'value' => 'TestApp'
		)));
		$this->assertEqual('error', $this->CoreSettings->Session->read('Message.flash.params.class'));
		$this->assertNull($this->CoreSettings->redirectUrl);
	}

}
