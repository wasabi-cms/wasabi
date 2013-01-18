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
App::uses('GroupsController', 'Core.Controller');

class GroupsTestController extends GroupsController {

	public $redirectUrl;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
		return false;
	}

}

/**
 * @property GroupsTestController $GroupsController
 */

class GroupsControllerTest extends ControllerTestCase {

	public $fixtures = array('plugin.core.group');

	public function setUp() {
		$this->GroupsController = new GroupsTestController();
		$this->GroupsController->constructClasses();
		$this->GroupsController->Components->init($this->GroupsController);
		/*$this->UsersController->viewVars['backend_menu_for_layout'] = array(
			'primary' => array(
				array(
					'url' => array(
						'plugin' => 'core',
						'controller' => 'users',
						'action' => 'index'
					)
				)
			)
		);*/

		parent::setUp();
	}

	public function testRequiredModelsAreSetup() {
		$this->assertTrue(in_array('Core.Group', $this->GroupsController->uses));
	}

	public function testIndexAction() {
		$this->GroupsController->index();
		$this->assertTrue(isset($this->GroupsController->viewVars['groups']));

		$expected = array(
			array(
				'Group' => array(
					'id' => '1',
					'name' => 'Administrator',
					'user_count' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'Group' => array(
					'id' => '2',
					'name' => 'Manager',
					'user_count' => 0,
					'created' => '2013-01-12 15:00:00',
					'modified' => '2013-01-12 15:00:00'
				)
			)
		);
		$result = $this->GroupsController->viewVars['groups'];
		var_dump($result);
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		unset($this->GroupsController);

		parent::tearDown();
	}

}
