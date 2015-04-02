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

class AddCmsPageLayoutAttributesTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('cms_page_layout_attributes', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'cms_page_id' => array('type' => 'integer', 'null' => false),
			'cms_layout' => array('type' => 'string', 'null' => false),
			'cms_layout_attribute' => array('type' => 'string', 'null' => false),
			'content' => array('type' => 'text', 'null' => true, 'default' => ''),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'cms_page_id' => array(
					'column' => 'cms_page_id'
				),
				'cms_layout' => array(
					'column' => 'cms_layout'
				),
				'cms_layout_attribute' => array(
					'column' => 'cms_layout_attribute'
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
		$this->dropTable('cms_page_layout_attributes');
	}

}
