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
App::uses('Group', 'Core.Model');

/**
 * @property Group $Group
 */

class GroupTest extends CakeTestCase {

	public $fixtures = array('plugin.core.group', 'plugin.core.user');

	public function setUp() {
		$this->Group = ClassRegistry::init('Core.Group');

		parent::setUp();
	}

	public function testGroupHasManyUser() {
		$this->assertTrue(array_key_exists('User', $this->Group->hasMany));
	}

	public function testFindAll() {
		$this->assertNotEmpty($this->Group->findAll());

		$result = $this->Group->findAll(array('conditions' => array('id' => 1)));
		$expected = array(
			array(
				'Group' => array(
					'id' => 1,
					'name' => 'Administrator',
					'user_count' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function testFindById() {
		$result = $this->Group->findById(1);
		$expected = array(
			'Group' => array(
				'id' => '1',
				'name' => 'Administrator',
				'user_count' => 2,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->Group->findById(100);
		$this->assertEmpty($result);

		$result = $this->Group->findById(1, array(
			'fields' => array(
				'Group.id'
			),
			'contain' => array(
				'User' => array(
					'fields' => array(
						'User.id'
					)
				)
			)
		));
		$expected = array(
			'Group' => array(
				'id' => 1
			),
			'User' => array(
				array(
					'id' => 1,
					'group_id' => 1
				),
				array(
					'id' => 2,
					'group_id' => 1
				)
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		unset($this->Group);

		parent::tearDown();
	}

}
