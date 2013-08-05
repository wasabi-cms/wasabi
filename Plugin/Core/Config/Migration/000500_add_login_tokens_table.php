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

class AddLoginTokensTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('login_tokens', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'user_id' => array('type' => 'integer', 'null' => false),
			'token' => array('type' => 'string', 'length' => 32, 'null' => false),
			'expires' => array('type' => 'datetime', 'null' => false),
			'created' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'user_id' => array(
					'column' => 'user_id'
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
		$this->dropTable('login_tokens');
	}

}
