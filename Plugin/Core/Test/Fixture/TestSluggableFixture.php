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

class TestSluggableFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => null),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => null),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => null),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'slug' => array('type' => 'string', 'length' => 255, 'null' => false),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'parent_id' => NULL,
				'lft' => 1,
				'rght' => 7,
				'name' => 'Hello World',
				'slug' => 'hello-world',
				'created' => '2013-03-19 07:00:00',
				'modified' => '2013-03-19 07:00:00'
			),
			array(
				'id' => 2,
				'parent_id' => 1,
				'lft' => 2,
				'rght' => 6,
				'name' => 'Foo Bar',
				'slug' => 'foo-bar',
				'created' => '2013-03-19 08:00:00',
				'modified' => '2013-03-19 08:00:00'
			),
			array(
				'id' => 3,
				'parent_id' => 2,
				'lft' => 3,
				'rght' => 4,
				'name' => 'Bar Foo',
				'slug' => 'bar-foo',
				'created' => '2013-03-19 09:00:00',
				'modified' => '2013-03-19 09:00:00'
			),
			array(
				'id' => 4,
				'parent_id' => 2,
				'lft' => 5,
				'rght' => 6,
				'name' => 'Bar Foo',
				'slug' => 'bar-foo-1',
				'created' => '2013-03-19 10:00:00',
				'modified' => '2013-03-19 10:00:00'
			),
		);
		parent::init();
	}

}
