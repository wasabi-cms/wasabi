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
App::uses('UsersController', 'Core.Controller');

class UsersTestController extends UsersController {

	public $redirectUrl;

	public $renderView;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}

	public function prepareAddEdit() {
		$this->_prepareAddEdit();
	}

	public function render($view = null, $layout = null) {
		$this->renderView = $view;
	}

}

/**
 * @property UsersTestController $Users
 * @method testAction
 */

class UsersControllerTest extends CoreControllerTest {

	public $fixtures = array('plugin.core.user', 'plugin.core.group', 'plugin.core.language', 'plugin.core.login_token', 'plugin.core.core_setting');

	public function setUp() {
		$this->Users = $this->generate('UsersTest');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Users);

		parent::tearDown();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.User', $this->Users->uses));
	}

	public function testIndexAction() {
		$this->testAction('/' . $this->backendPrefix . '/users', array('method' => 'get'));

		$this->assertInternalType('string', $this->Users->viewVars['title_for_layout']);
		$this->assertTrue(isset($this->Users->viewVars['users']));
		$this->assertNotEmpty($this->Users->viewVars['users']);
	}

	public function testAddActionGet() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/add', array('method' => 'get'));

		$this->assertNull($this->Users->redirectUrl);
	}

	public function testAddActionPostWithInvalidData() {
		$this->_loginUser();

		$userCount = $this->Users->User->find('count');

		$this->testAction('/' . $this->backendPrefix . '/users/add', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'test user',
					'password_unencrypted' => 'testpasswd',
					'password_confirmation' => 'wrongconfirmation',
					'group_id' => 1,
					'language_id' => 1
				)
			)
		));

		$this->assertEqual($userCount, $this->Users->User->find('count'));
		$this->assertFalse($this->Users->User->hasAny(array('username' => 'test user')));
		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Users->redirectUrl);
	}

	public function testAddActionPost() {
		$this->_loginUser();

		$userCount = $this->Users->User->find('count');

		$this->testAction('/' . $this->backendPrefix . '/users/add', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'test user',
					'password_unencrypted' => 'testpasswd',
					'password_confirmation' => 'testpasswd',
					'group_id' => 1,
					'language_id' => 1
				)
			)
		));

		$this->assertEqual(($userCount + 1), $this->Users->User->find('count'));
		$this->assertTrue($this->Users->User->hasAny(array('username' => 'test user')));
		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testEditActionWithoutId() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/edit', array('method' => 'get'));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testEditActionDisallowEditOfGlobalAdminByOtherUsers() {
		$this->_loginUser(2);

		$this->testAction('/' . $this->backendPrefix . '/users/edit/1', array('method' => 'get'));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testEditActionGet() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/edit/1', array('method' => 'get'));

		$this->assertEqual($this->Users->request->data, $this->Users->User->findById(1));
		$this->assertFalse(isset($this->Users->User->validate['password_unencrypted']));
		$this->assertFalse(isset($this->Users->User->validate['password_confirmation']));
		$this->assertFalse($this->Users->Session->check('Message.flash'));
		$this->assertEqual('add', $this->Users->renderView);
	}

	public function testEditActionPostWithInvalidData() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/edit/1', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'id' => 1,
					'username' => 'a'
				)
			)
		));

		$this->assertFalse($this->Users->User->hasAny(array('username' => 'a')));
		$this->assertNotEmpty($this->Users->User->validationErrors);
		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Users->redirectUrl);
	}

	public function testEditActionPost() {
		$this->_loginUser();

		$userCount = $this->Users->User->find('count');

		$this->testAction('/' . $this->backendPrefix . '/users/edit/1', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'id' => 1,
					'username' => 'admin modified'
				)
			)
		));

		$this->assertEqual($userCount, $this->Users->User->find('count'));
		$this->assertTrue($this->Users->User->hasAny(array('username' => 'admin modified')));
		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testDeleteActionGetThrowsException() {
		$this->_loginUser();

		$this->setExpectedException('MethodNotAllowedException');

		$this->testAction('/' . $this->backendPrefix . '/users/delete', array('method' => 'get'));
	}

	public function testDeleteActionPostNoId() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/delete', array('method' => 'post'));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testDeleteActionUserCannotDeleteOwnAccount() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/delete/1', array('method' => 'post'));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testDeleteActionNonExistentId() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/users/delete/99', array('method' => 'post'));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testDeleteActionPost() {
		$this->_loginUser();

		$userCount = $this->Users->User->find('count');

		$this->testAction('/' . $this->backendPrefix . '/users/delete/2', array('method' => 'post'));

		$this->assertEqual(($userCount - 1), $this->Users->User->find('count'));
		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'index'), $this->Users->redirectUrl);
	}

	public function testLoginActionGet() {
		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'get'));

		$this->assertEqual('Core.login', $this->Users->layout);
		$this->assertTrue(isset($this->Users->viewVars['title_for_layout']));
		$this->assertFalse($this->Users->Session->check('login_referer'));
	}

	public function testLoginActionGetLoginRefererIsCarriedOn() {
		$this->Users->Session->write('login_referer', '/foo');

		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'get'));

		$this->assertTrue($this->Users->Session->check('login_referer'));
	}

	public function testLoginActionGetWithLoggedInUser() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'get'));

		$this->assertFalse($this->Users->Session->check('login_referer'));

		$expected = array('plugin' => 'core', 'controller' => 'dashboard', 'action' => 'index');
		$result = $this->Users->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testLoginActionGetWithLoggedInUserAndCustomLoginReferer() {
		$this->_loginUser();
		$this->Users->Session->write('login_referer', '/foo');

		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'get'));

		$this->assertFalse($this->Users->Session->check('login_referer'));
		$this->assertEqual('/foo', $this->Users->redirectUrl);
	}

	public function testLoginActionPostWithoutData() {
		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'post'));

		$this->assertTrue($this->Users->Session->check('Message.flash'));
		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertTrue($this->Users->Session->check('login_referer'));
	}

	public function testLoginActionPostWithoutDataAndCustomLoginReferer() {
		$this->Users->Session->write('login_referer', '/foo');

		$this->testAction('/' . $this->backendPrefix . '/login', array('method' => 'post'));

		$this->assertTrue($this->Users->Session->check('Message.flash'));
		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertTrue($this->Users->Session->check('login_referer'));
		$this->assertEqual('/foo', $this->Users->Session->read('login_referer'));
	}

	public function testLoginActionPostWithInvalidForm() {
		$this->testAction('/' . $this->backendPrefix . '/login', array(
			'method' => 'post',
			'data' => array(
				'Foo' => 'bar'
			)
		));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Users->redirectUrl);
	}

	public function testLoginActionPostWithValidFormAndInvalidData() {
		$this->testAction('/' . $this->backendPrefix . '/login', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'foo',
					'password' => 'bar'
				)
			)
		));

		$this->assertEqual('error', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertNull($this->Users->redirectUrl);
	}

	public function testLoginActionPost() {
		$this->testAction('/' . $this->backendPrefix . '/login', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'admin',
					'password' => 'admin'
				)
			)
		));

		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));

		$expected = array('plugin' => 'core', 'controller' => 'dashboard', 'action' => 'index');
		$result = $this->Users->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function testLoginActionPostWithCustomLoginReferer() {
		$this->Users->Session->write('login_referer', '/foo');

		$this->testAction('/' . $this->backendPrefix . '/login', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'admin',
					'password' => 'admin'
				)
			)
		));

		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual('/foo', $this->Users->redirectUrl);
		$this->assertFalse($this->Users->Session->check('login_referer'));
	}

	public function testLoginActionPostWithRemember() {
		$this->testAction('/' . $this->backendPrefix . '/login', array(
			'method' => 'post',
			'data' => array(
				'User' => array(
					'username' => 'admin',
					'password' => 'admin',
					'remember' => '1'
				)
			)
		));

		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertArrayHasKey('me', $this->Users->Authenticator->Cookie->read());
	}

	public function testLogout() {
		$this->_loginUser();

		$this->testAction('/' . $this->backendPrefix . '/logout', array('method' => 'get'));

		$this->assertEmpty($this->Users->Authenticator->get());
		$this->assertTrue($this->Users->Session->check('Message.flash'));
		$this->assertEqual('success', $this->Users->Session->read('Message.flash.params.class'));
		$this->assertEqual(array('action' => 'login'), $this->Users->redirectUrl);
	}

}
