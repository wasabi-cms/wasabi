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

class CoreSettingFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'application_name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'enable_caching' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'cache_time'  => array('type' => 'string', 'length' => 255, 'null' => false),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'application_name' => 'TestApp',
				'enable_caching' => 1,
				'cache_time' => '14 days',
				'created' => '2013-03-05 09:00:00',
				'modified' => '2013-03-05 09:00:00'
			)
		);
		parent::init();
	}

}
