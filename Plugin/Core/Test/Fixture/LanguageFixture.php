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

class LanguageFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'locale' => array('type' => 'string', 'length' => 2, 'null' => false),
		'iso' => array('type' => 'string', 'length' => 3, 'null' => false),
		'lang' => array('type' => 'string', 'length' => 5, 'null' => false),
		'available_at_frontend' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'available_at_backend' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'position' => array('type' => 'integer', 'null' => true),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'name' => 'English',
				'locale' => 'en',
				'iso' => 'eng',
				'lang' => 'en-US',
				'available_at_frontend' => 1,
				'available_at_backend' => 1,
				'position' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			),
			array(
				'id' => 2,
				'name' => 'Deutsch',
				'locale' => 'de',
				'iso' => 'deu',
				'lang' => 'de-DE',
				'available_at_frontend' => 0,
				'available_at_backend' => 1,
				'position' => 2,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			),
			array(
				'id' => 3,
				'name' => 'Asd',
				'locale' => 'as',
				'iso' => 'asd',
				'lang' => 'as-AS',
				'available_at_frontend' => 0,
				'available_at_backend' => 0,
				'position' => 3,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		parent::init();
	}

}
