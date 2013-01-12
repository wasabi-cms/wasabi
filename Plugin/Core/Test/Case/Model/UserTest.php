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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('User', 'Core.Model');

/**
 * @property User $User
 */

class UserTest extends CakeTestCase {

	public $fixtures = array('plugin.core.user');

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Core.User');
	}

	public function testGetAll() {
		$result = $this->User->getAll();
		$expected = array(
			array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => md5('admin'), 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00')),
			array('User' => array('id' => 2, 'group_id' => 1, 'language_id' => 1, 'username' => 'test', 'password' => md5('test'), 'created' => '2013-01-12 15:00:00', 'modified' => '2013-01-12 15:00:00'))
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->getAll(array('conditions' => array('id' => 1)));
		$expected = array(
			array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => md5('admin'), 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00'))
		);
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		unset($this->User);
	}

}
