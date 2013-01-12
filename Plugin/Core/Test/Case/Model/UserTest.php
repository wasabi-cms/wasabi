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

App::uses('ClassRegistry', 'Utility');
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

	public function testFindAll() {
		$result = $this->User->findAll();
		$expected = array(
			array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO', 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00')),
			array('User' => array('id' => 2, 'group_id' => 1, 'language_id' => 1, 'username' => 'test', 'password' => '$2a$10$i4q2qRWt5dX5O/C.Nldq5evjpY3MNMlG3K4BrxsXH7zBZmxqwzAUO', 'created' => '2013-01-12 15:00:00', 'modified' => '2013-01-12 15:00:00'))
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->findAll(array('conditions' => array('id' => 1)));
		$expected = array(
			array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO', 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00'))
		);
		$this->assertEqual($expected, $result);
	}

	public function testFindWithCredentials() {
		$expected = array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO', 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00'));

		$result = $this->User->findWithCredentials('admin', 'admin');
		$this->assertEqual($expected, $result);

		$result = $this->User->findWithCredentials('admin', 'foo');
		$this->assertFalse($result);

		$result = $this->User->findWithCredentials('foo', 'bar');
		$this->assertFalse($result);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->User);
	}

}
