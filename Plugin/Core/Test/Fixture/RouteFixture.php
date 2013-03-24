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

class RouteFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'url' => array('type' => 'text', 'null' => false),
		'plugin' => array('type' => 'string', 'length' => 255, 'null' => false),
		'controller' => array('type' => 'string', 'length' => 255, 'null' => false),
		'action' => array('type' => 'string', 'length' => 255, 'null' => false),
		'params' => array('type' => 'text', 'null' => false),
		'redirect_to' => array('type' => 'integer', 'null' => true, 'default' => null),
		'status_code' => array('type' => 'integer', 'null' => true, 'default' => null),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'url' => '/tests/foo',
				'plugin' => '',
				'controller' => 'tests',
				'action' => 'foo',
				'params' => '',
				'redirect_to' => null,
				'status_code' => null,
				'created' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			),
			array(
				'id' => 2,
				'url' => '/tests/foo/3',
				'plugin' => '',
				'controller' => 'tests',
				'action' => 'foo',
				'params' => '3',
				'redirect_to' => null,
				'status_code' => null,
				'created' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			),
			array(
				'id' => 3,
				'url' => '/',
				'plugin' => 'awesome',
				'controller' => 'tests',
				'action' => 'foo',
				'params' => '3',
				'redirect_to' => null,
				'status_code' => null,
				'created' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			),
			array(
				'id' => 4,
				'url' => '/awesome/tests/foo/4',
				'plugin' => 'awesome',
				'controller' => 'tests',
				'action' => 'foo',
				'params' => '4',
				'redirect_to' => 2,
				'status_code' => 307,
				'created' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			),
			array(
				'id' => 5,
				'url' => '/awesome/tests/foo/5',
				'plugin' => 'awesome',
				'controller' => 'tests',
				'action' => 'foo',
				'params' => '5',
				'redirect_to' => 2,
				'status_code' => null,
				'created' => '0000-00-00 00:00:00',
				'modified' => '0000-00-00 00:00:00'
			)
		);
		parent::init();
	}

}
