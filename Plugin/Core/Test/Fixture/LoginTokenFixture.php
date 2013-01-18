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

class LoginTokenFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false),
		'token' => array('type' => 'string', 'length' => 32, 'null' => false),
		'expires' => 'datetime',
		'created' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'user_id' => 1,
				'token' => 'i_am_a_very_secret_token',
				'expires' => date('Y-m-d H:i:s', strtotime('2 weeks')),
				'created' => '2013-01-12 14:00:00'
			)
		);
		parent::init();
	}

}
