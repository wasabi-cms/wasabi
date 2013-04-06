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

class TestPluginCommentFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'test_plugin_post_id' => array('type' => 'integer', 'null' => false),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
		'comment' => array('type' => 'text', 'null' => false)
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'test_plugin_post_id' => 1,
				'name' => 'Carol',
				'comment' => 'Carol’s comment on Post 1'
			),
			array(
				'id' => 2,
				'test_plugin_post_id' => 1,
				'name' => 'Julia',
				'comment' => 'Julia’s comment on Post 1'
			),
			array(
				'id' => 3,
				'test_plugin_post_id' => 2,
				'name' => 'John',
				'comment' => 'John’s comment on Post 2'
			)
		);
		parent::init();
	}

}
