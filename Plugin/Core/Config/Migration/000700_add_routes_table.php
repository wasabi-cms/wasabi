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

class AddRoutesTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('routes', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'url' => array('type' => 'text', 'null' => false),
			'plugin' => array('type' => 'string', 'length' => 255, 'null' => false),
			'controller' => array('type' => 'string', 'length' => 255, 'null' => false),
			'action' => array('type' => 'string', 'length' => 255, 'null' => false),
			'params' => array('type' => 'text', 'null' => false),
			'redirect_to' => array('type' => 'integer', 'null' => true),
			'status_code' => array('type' => 'integer', 'null' => true, 'default' => null),
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
		$this->dropTable('routes');
	}

}
