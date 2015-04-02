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
 * @subpackage    Wasabi.Plugin.Cms.Config.Migration
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Migration', 'Migrations.Model');

class AddCmsPagesTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('cms_pages', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'parent_id' => array('type' => 'integer', 'null' => false),
			'lft' => array('type' => 'integer', 'null' => false),
			'rght' => array('type' => 'integer', 'null' => false),
			'page_type' => array('type' => 'string', 'null' => false),
			'collection' => array('type' => 'string', 'null' => false),
			'collection_item' => array('type' => 'string', 'null' => false),
			'cms_layout' => array('type' => 'string', 'null' => false),
			'name' => array('type' => 'string', 'null' => false),
			'slug' => array('type' => 'string', 'null' => false),
			'page_title' => array('type' => 'string', 'null' => false),
			'meta_description' => array('type' => 'text', 'null' => false),
			'status' => array('type' => 'string', 'null' => false),
			'cached' => array('type' => 'boolean', 'null' => false, 'default' => 0),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'parent_id' => array(
					'column' => 'parent_id'
				)
			)
		));
	}

	/**
	 * Migrate down
	 *
	 * @return void
	 */
	public function down() {
		$this->dropTable('cms_pages');
	}

}
