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

class GroupFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'user_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'name' => 'Administrator',
				'user_count' => 2,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'),
			array(
				'id' => 2,
				'name' => 'Manager',
				'user_count' => 1,
				'created' => '2013-01-12 15:00:00',
				'modified' => '2013-01-12 15:00:00'
			)
		);
		parent::init();
	}

}
