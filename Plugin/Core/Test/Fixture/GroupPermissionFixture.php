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

class GroupPermissionFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'group_id' => array('type' => 'integer', 'null' => false),
		'path' => array('type' => 'string', 'null' => false),
		'plugin' => array('type' => 'string', 'null' => false),
		'controller' => array('type' => 'string', 'null' => false),
		'action' => array('type' => 'string', 'null' => false),
		'allowed' => array('type' => 'boolean', 'null' => false, 'default' => 0),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array();
		parent::init();
	}

}
