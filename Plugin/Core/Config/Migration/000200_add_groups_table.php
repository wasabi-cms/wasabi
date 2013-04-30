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

App::uses('ClassRegistry', 'Utility');
App::uses('Group', 'Core.Model');
App::uses('Migration', 'Migrations.Model');

class AddGroupsTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('groups', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'name' => array('type' => 'string', 'null' => false),
			'user_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				)
			)
		));

		$group = ClassRegistry::init('Core.Group');
		$group->create(array(
			'name' => 'Administrator'
		));
		$group->save();
	}

	/**
	 * Migrate down
	 *
	 * @return void
	 */
	public function down() {
		$this->dropTable('groups');
	}

}
