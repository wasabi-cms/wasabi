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
 * @subpackage    Wasabi.Plugin.Core.Config.Migration
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Migration', 'Migrations.Model');

class AddMenuItemsTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('menu_items', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'menu_id' => array('type' => 'integer', 'null' => false),
			'name' => array('type' => 'string', 'null' => false),
			'item' => array('type' => 'text', 'null' => false),
			'type' => array('type' => 'string', 'null' => false),
			'position' => array('type' => 'integer', 'null' => false, 'default' => 9999),
			'external_link' => array('type' => 'text', 'null' => true),
			'foreign_model' => array('type' => 'string', 'null' => true),
			'foreign_id' => array('type' => 'integer', 'null' => true),
			'plugin' => array('type' => 'string', 'null' => true),
			'controller' => array('type' => 'string', 'null' => true),
			'action' => array('type' => 'string', 'null' => true),
			'params' => array('type' => 'text', 'null' => true),
			'query' => array('type' => 'text', 'null' => true),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
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
		$this->dropTable('menu_items');
	}

}
