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

App::uses('CakeTestCase', 'TestSuite');
App::uses('ClassRegistry', 'Utility');
App::uses('Hash', 'Utility');
App::uses('User', 'Core.Model');

/**
 * @property User $User
 */

class UserTest extends CakeTestCase {

	public $fixtures = array('plugin.core.user', 'plugin.core.group');

	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Core.User');
	}

	public function testUserBelongsToGroup() {
		$this->assertTrue(array_key_exists('Group', $this->User->belongsTo));
	}

	public function testUserHasManyLoginToken() {
		$this->assertTrue(array_key_exists('LoginToken', $this->User->hasMany));
		$this->assertTrue(Hash::get($this->User->hasMany, 'LoginToken.dependent'));
	}

	public function testFindAll() {
		$result = $this->User->findAll();
		$expected = array(
			array(
				'User' => array(
					'id' => 1,
					'group_id' => 1,
					'language_id' => 1,
					'username' => 'admin',
					'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
					'active' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'User' => array(
					'id' => 2,
					'group_id' => 1,
					'language_id' => 1,
					'username' => 'test',
					'password' => '$2a$10$i4q2qRWt5dX5O/C.Nldq5evjpY3MNMlG3K4BrxsXH7zBZmxqwzAUO',
					'active' => 0,
					'created' => '2013-01-12 15:00:00',
					'modified' => '2013-01-12 15:00:00'
				)
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->findAll(array('conditions' => array('id' => 1)));
		$expected = array(
			array('User' => array('id' => 1, 'group_id' => 1, 'language_id' => 1, 'username' => 'admin', 'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO', 'active' => 1, 'created' => '2013-01-12 14:00:00', 'modified' => '2013-01-12 14:00:00'))
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->findAll(array(
			'fields' => array(
				'User.id',
				'Group.*'
			),
			'conditions' => array(
				'User.id' => 1
			),
			'contain' => array(
				'Group'
			)
		));
		$expected = array(
			array(
				'User' => array(
					'id' => 1
				),
				'Group' => array(
					'id' => 1,
					'name' => 'Administrator',
					'user_count' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
		);
		$this->assertEqual($expected, $result);
	}

	public function testFindActiveByCredentials() {
		$result = $this->User->findActiveByCredentials('admin', 'admin');
		$expected = array(
			'User' => array(
				'id' => 1,
				'group_id' => 1,
				'language_id' => 1,
				'username' => 'admin',
				'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
				'active' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->findActiveByCredentials('admin', 'foo');
		$this->assertFalse($result);

		$result = $this->User->findActiveByCredentials('test', 'test');
		$this->assertFalse($result);

		$result = $this->User->findActiveByCredentials('foo', 'bar');
		$this->assertFalse($result);

		$result = $this->User->findActiveByCredentials('admin', 'admin', array(
			'contain' => array(
				'Group'
			)
		));
		$expected = array(
			'User' => array(
				'id' => 1,
				'group_id' => 1,
				'language_id' => 1,
				'username' => 'admin',
				'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
				'active' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			),
			'Group' => array(
				'id' => 1,
				'name' => 'Administrator',
				'user_count' => 2,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function testFindById() {
		$result = $this->User->findById(1);
		$expected = array(
			'User' => array(
				'id' => 1,
				'group_id' => 1,
				'language_id' => 1,
				'username' => 'admin',
				'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
				'active' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->User->findById(100);
		$this->assertEmpty($result);

		$result = $this->User->findById(1, array(
			'fields' => array(
				'User.id',
				'Group.*'
			),
			'contain' => array(
				'Group'
			)
		));
		$expected = array(
			'User' => array(
				'id' => 1
			),
			'Group' => array(
				'id' => 1,
				'name' => 'Administrator',
				'user_count' => 2,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function testAuthenticate() {
		$expected = array();
		$result = $this->User->authenticate('invalidMethod');
		$this->assertEqual($expected, $result);

		$expected = array();
		$result = $this->User->authenticate('guest');
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->User);
	}

}
