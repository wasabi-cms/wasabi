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

class TestPluginArticleFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'test_plugin_category_id' => array('type' => 'integer', 'null' => false),
		'title' => array('type' => 'string', 'length' => 255, 'null' => false)
	);

	public $records;

	public function init() {
		$this->records = array(
			array(
				'id' => 1,
				'test_plugin_category_id' => 1,
				'title' => 'Article 1'
			),
			array(
				'id' => 2,
				'test_plugin_category_id' => 2,
				'title' => 'Article 2'
			),
			array(
				'id' => 3,
				'test_plugin_category_id' => 3,
				'title' => 'Article 3'
			),
			array(
				'id' => 4,
				'test_plugin_category_id' => 1,
				'title' => 'Article 4'
			)
		);
		parent::init();
	}

}
