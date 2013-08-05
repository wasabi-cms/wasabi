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
App::uses('User', 'Core.Model');
App::uses('Migration', 'Migrations.Model');

class AddUsersTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('users', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'group_id' => array('type' => 'integer', 'null' => false),
			'language_id' => array('type' => 'integer', 'null' => false),
			'username' => array('type' => 'string', 'null' => false),
			'password' => array('type' => 'string', 'length' => 60, 'null' => false),
			'active' => array('type' => 'boolean', 'null' => false, 'default' => 0),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'group_id' => array(
					'column' => 'group_id'
				),
				'language_id' => array(
					'column' => 'language_id'
				)
			)
		));

		$user = ClassRegistry::init('Core.User');
		$user->create(array(
			'group_id' => 1,
			'language_id' => 1,
			'username' => 'admin',
			'password' => '$2a$10$XgE0KcjO4WNIXZIPk.6dQ.ZXTCf5pxVxdx9SIh5p5JMe9iSd8ceIO',
			'active' => true
		));
		$user->save();
	}

	/**
	 * Migrate down
	 *
	 * @return void
	 */
	public function down() {
		$this->dropTable('users');
	}

}
