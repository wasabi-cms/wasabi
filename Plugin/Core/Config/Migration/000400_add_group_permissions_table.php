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

class AddGroupPermissionsTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('group_permissions', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'group_id' => array('type' => 'integer', 'null' => false),
			'path' => array('type' => 'string', 'null' => false),
			'plugin' => array('type' => 'string', 'null' => false),
			'controller' => array('type' => 'string', 'null' => false),
			'action' => array('type' => 'string', 'null' => false),
			'allowed' => array('type' => 'boolean', 'null' => false, 'default' => 0),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'group_id' => array(
					'column' => 'group_id'
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
		$this->dropTable('group_permissions');
	}

}
