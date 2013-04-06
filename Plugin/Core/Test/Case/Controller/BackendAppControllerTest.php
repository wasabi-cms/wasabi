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

App::uses('AuthenticatorComponent', 'Core.Controller/Component');
App::uses('BackendAppController', 'Core.Controller');
App::uses('Configure', 'Core');
App::uses('CoreControllerTest', 'Core.Test/TestSuite');

class BackendAppTestController extends BackendAppController {

	public $redirectUrl;
	public $checkPermissionsCalled = false;
	public $setupBackendCalled = false;

	public function checkPermisions() {
		$this->_checkPermissions();
	}

	public function setupBackend() {
		$this->_setupBackend();
	}

	public function loadBackendMenu() {
		$this->_loadBackendMenu();
	}

	public function loadLanguages() {
		$this->_loadLanguages();
	}

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return false;
	}

	protected function _checkPermissions() {
		$this->checkPermissionsCalled = true;
		parent::_checkPermissions();
	}

	protected function _setupBackend() {
		$this->setupBackendCalled = true;
		parent::_setupBackend();
	}

}

class BackendAppTestTwoController extends BackendAppTestController {

	public $triggerEventCalled = false;

	protected function _triggerEvent(&$origin, $event_name, $data = null) {
		$this->triggerEventCalled = true;
		return array();
	}

}

class AuthenticatorTest1Component extends AuthenticatorComponent {

	public function __construct(ComponentCollection $collection) {
		return parent::__construct($collection, array(
			'model' => 'Foo',
			'sessionKey' => 'Auth',
			'cookieKey' => 'AuthRemember'
		));
	}

	public function get($field = null) {
		return array();
	}

}

class AuthenticatorTest2Component extends AuthenticatorTest1Component {

	public function get($field = null) {
		return array(
			'User' => array(
				'id' => 1,
				'username' => 'admin'
			)
		);
	}

}

/**
 * @property BackendAppTestController|BackendAppTestTwoController $BackendAppController
 */

class BackendAppControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.language', 'plugin.core.core_setting');

	public function setUp() {
		$this->BackendAppController = new BackendAppTestController();
		$this->BackendAppController->constructClasses();
		$this->BackendAppController->request = new CakeRequest();

		parent::setUp();
	}

	public function tearDown() {
		unset($this->BackendAppController);

		parent::tearDown();
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

	public function testBeforeFilter() {
		$this->BackendAppController->request->params = Router::parse('/backend/users');
		$this->BackendAppController->request->url = 'backend/users';
		$this->BackendAppController->Authenticator = new AuthenticatorTest1Component($this->BackendAppController->Components);
		$this->BackendAppController->beforeFilter();

		$this->assertTrue($this->BackendAppController->checkPermissionsCalled);
		$this->assertTrue($this->BackendAppController->setupBackendCalled);
	}

	public function testCheckPermissions() {
		$this->BackendAppController->request->params = Router::parse('/backend/users');
		$this->BackendAppController->request->url = 'backend/users';
		$this->BackendAppController->Authenticator = new AuthenticatorTest1Component($this->BackendAppController->Components);

		$this->BackendAppController->checkPermisions();
		$this->assertTrue($this->BackendAppController->Session->check('login_referer'));

		$expected = '/backend/users';
		$result = $this->BackendAppController->Session->read('login_referer');
		$this->assertEqual($expected, $result);

		$expected = array(
			'plugin' => 'core',
			'controller' => 'users',
			'action' => 'login'
		);
		$result = $this->BackendAppController->redirectUrl;
		$this->assertEqual($expected, $result);

		$this->BackendAppController->Session->delete('login_referer');
		$this->BackendAppController->Authenticator = new AuthenticatorTest2Component($this->BackendAppController->Components);

		$this->BackendAppController->checkPermisions();
		$this->assertFalse($this->BackendAppController->Session->check('login_referer'));

		$this->BackendAppController->request->params = Router::parse('/backend/login');
		$this->BackendAppController->request->url = 'backend/login';
		$this->BackendAppController->Authenticator = new AuthenticatorTest1Component($this->BackendAppController->Components);

		$this->BackendAppController->checkPermisions();
		$this->assertFalse($this->BackendAppController->Session->check('login_referer'));
	}

	public function testSetupBackend() {
		$this->BackendAppController->request->params = Router::parse('/backend/users');
		$this->BackendAppController->request->url = 'backend/users';
		$this->BackendAppController->setupBackend();
		$expected = 'Core.default';
		$result = $this->BackendAppController->layout;
		$this->assertEqual($expected, $result);
	}

	public function testLoadBackendMenu() {
		$this->BackendAppController->request->params = Router::parse('/backend/users');
		$this->BackendAppController->request->url = 'backend/users';
		$this->BackendAppController->loadBackendMenu();
		$this->assertTrue(isset($this->BackendAppController->viewVars['backend_menu_for_layout']));
		$this->assertTrue(isset($this->BackendAppController->viewVars['backend_menu_for_layout']['primary']));
		$this->assertTrue(isset($this->BackendAppController->viewVars['backend_menu_for_layout']['secondary']));
	}

	public function testLoadBackendMenuReturnsIfNoMenusAreSetup() {
		$this->BackendAppController = new BackendAppTestTwoController(new CakeRequest(), new CakeResponse());
		$this->BackendAppController->constructClasses();
		$this->BackendAppController->Components->init($this->BackendAppController);
		$this->BackendAppController->loadBackendMenu();

		$this->assertTrue($this->BackendAppController->triggerEventCalled);
	}

	public function testLoadLanguages() {
		Cache::delete('languages', 'core.infinite');
		$this->assertFalse(Cache::read('languages', 'core.infinite'));
		$this->BackendAppController->loadLanguages();

		$this->assertNotEmpty(Configure::read('Languages.frontend'));
		$this->assertNotEmpty(Configure::read('Languages.backend'));
		$this->assertEqual(1, Configure::read('Wasabi.backend_language.id'));
		$this->assertEqual('eng', Configure::read('Config.language'));

		$this->BackendAppController->Session->write('Auth', array(
			'User' => array(
				'id' => 1
			),
			'Language' => array(
				'id' => 2,
				'iso' => 'deu'
			)
		));
		$this->BackendAppController->loadLanguages();

		$this->assertEqual(2, Configure::read('Wasabi.backend_language.id'));
		$this->assertEqual('deu', Configure::read('Config.language'));

		$this->BackendAppController->Session->write('Wasabi.content_language_id', 1);
		$this->BackendAppController->loadLanguages();

		$this->assertEqual(1, Configure::read('Wasabi.content_language.id'));
	}

}
