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
App::uses('UsersController', 'Core.Controller');

class UsersTestController extends UsersController {

	public $redirectUrl;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return false;
	}

}

/**
 * @property UsersTestController $UsersController
 */

class UsersControllerTest extends ControllerTestCase {

	public $fixtures = array('plugin.core.user', 'plugin.core.group', 'plugin.core.language');

	public function setUp() {
		$this->UsersController = new UsersTestController();
		$this->UsersController->constructClasses();
		$this->UsersController->Components->init($this->UsersController);
		$this->UsersController->viewVars['backend_menu_for_layout'] = array(
			'primary' => array(
				array(
					'url' => array(
						'plugin' => 'core',
						'controller' => 'users',
						'action' => 'index'
					)
				)
			)
		);

		parent::setUp();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.User', $this->UsersController->uses));
	}

	public function testIndexAction() {
		$this->UsersController->index();
		$this->assertTrue(isset($this->UsersController->viewVars['users']));

		$expected = array(
			array(
				'User' => array(
					'id' => '1',
					'group_id' => '1',
					'language_id' => '1',
					'username' => 'admin',
					'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
					'active' => '1',
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				),
				'Group' => array(
					'id' => '1',
					'name' => 'Administrator',
					'user_count' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'User' => array(
					'id' => '2',
					'group_id' => '1',
					'language_id' => '1',
					'username' => 'test',
					'password' => '$2a$10$i4q2qRWt5dX5O/C.Nldq5evjpY3MNMlG3K4BrxsXH7zBZmxqwzAUO',
					'active' => '0',
					'created' => '2013-01-12 15:00:00',
					'modified' => '2013-01-12 15:00:00'
				),
				'Group' => array(
					'id' => '1',
					'name' => 'Administrator',
					'user_count' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$result = $this->UsersController->viewVars['users'];
		$this->assertEqual($expected, $result);
	}

	public function testLoginAction() {
		$this->UsersController->data = array();
		$this->UsersController->login();
		$expected = 'Core.login';
		$result = $this->UsersController->layout;
		$this->assertEqual($expected, $result);
		$this->assertTrue(isset($this->UsersController->viewVars['title_for_layout']));
		$this->assertFalse(isset($this->UsersController->viewVars['login_referer']));

		$this->UsersController->Session->write('login_referer', 'backend/users');
		$this->UsersController->data = array();
		$this->UsersController->login();
		$this->assertTrue(isset($this->UsersController->viewVars['login_referer']));
		$expected = 'backend/users';
		$result = $this->UsersController->viewVars['login_referer'];
		$this->assertEqual($expected, $result);
		$this->assertFalse($this->UsersController->Session->check('login_referer'));

		$this->UsersController->data = array(
			'User' => array(
				'username' => 'admin',
				'password' => 'admin'
			)
		);
		$this->UsersController->login();
		$this->assertTrue($this->UsersController->Session->check('Message.flash'));
		$expected = 'success';
		$result = $this->UsersController->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);
		$expected = array(
			'plugin' => 'core',
			'controller' => 'users',
			'action' => 'index'
		);
		$result = $this->UsersController->redirectUrl;
		$this->assertEqual($expected, $result);
		$this->UsersController->Session->delete('wasabi');

		$this->UsersController->data = array(
			'User' => array(
				'username' => 'admin',
				'password' => 'admin',
				'login_referer' => 'protected/url'
			)
		);
		$this->UsersController->login();
		$expected = '/protected/url';
		$result = $this->UsersController->redirectUrl;
		$this->assertEqual($expected, $result);
		$this->UsersController->Session->delete('wasabi');

		$this->UsersController->data = array(
			'User' => array(
				'username' => 'admin',
				'password' => 'foo'
			)
		);
		$this->UsersController->login();
		$this->assertTrue($this->UsersController->Session->check('Message.flash'));
		$expected = 'error';
		$result = $this->UsersController->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$this->UsersController->Session->delete('wasabi');
	}

	public function testLogout() {
		$this->UsersController->logout();

		$this->assertTrue($this->UsersController->Session->check('Message.flash'));

		$expected = 'success';
		$result = $this->UsersController->Session->read('Message.flash.params.class');
		$this->assertEqual($expected, $result);

		$expected = array(
			'action' => 'login'
		);
		$result = $this->UsersController->redirectUrl;
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		unset($this->UsersController);

		parent::tearDown();
	}

}
