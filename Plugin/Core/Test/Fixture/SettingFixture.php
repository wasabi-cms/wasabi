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

class SettingFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'scope' => array('type' => 'string', 'null' => false),
		'key' => array('type' => 'string', 'null' => false),
		'value' => array('type' => 'string', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => false),
		'modified' => array('type' => 'datetime', 'null' => false),
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'scope' => 'Core',
				'key' => 'application_name',
				'value' => 'TestApp',
				'created' => '2013-03-05 09:00:00',
				'modified' => '2013-03-05 09:00:00'
			)
		);
		parent::init();
	}

}
