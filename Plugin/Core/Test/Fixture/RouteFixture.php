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

		);
		parent::init();
	}

}
