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
 * @subpackage    Wasabi.Plugin.Core.Test.Fixture
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestFixture', 'TestSuite/Fixture');

class UserFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false),
		'language_id' => array('type' => 'integer', 'null' => false),
		'username' => array('type' => 'string', 'length' => 255, 'null' => false),
		'password' => array('type' => 'string', 'length' => 60),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'group_id' => 1,
				'language_id' => 1,
				'username' => 'admin',
				'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
				'active' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			),
			array(
				'id' => 2,
				'group_id' => 1,
				'language_id' => 1,
				'username' => 'test',
				'password' => '$2a$10$i4q2qRWt5dX5O/C.Nldq5evjpY3MNMlG3K4BrxsXH7zBZmxqwzAUO',
				'active' => 0,
				'created' => '2013-01-12 15:00:00',
				'modified' => '2013-01-12 15:00:00'
			),
			array(
				'id' => 3,
				'group_id' => 2,
				'language_id' => 1,
				'username' => 'manager',
				'password' => '$2a$10$i4q2qRWt5dX5O/C.Nldq5evjpY3MNMlG3K4BrxsXH7zBZmxqwzAUO',
				'active' => 0,
				'created' => '2013-01-12 15:00:00',
				'modified' => '2013-01-12 15:00:00'
			)
		);
		parent::init();
	}

}
