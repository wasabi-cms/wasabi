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
App::uses('CoreSetting', 'Core.Model');
App::uses('Migration', 'Migrations.Model');

class AddSettingsTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('settings', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'scope' => array('type' => 'string', 'null' => false),
			'key' => array('type' => 'string', 'null' => false),
			'value' => array('type' => 'text', 'null' => true),
			'serialized' => array('type' => 'boolean', 'null' => true),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				),
				'scope' => array(
					'column' => 'scope'
				)
			)
		));

		/**
		 * @var CoreSetting
		 */
		$setting = ClassRegistry::init('Core.CoreSetting');
		$setting->saveKeyValues(array(
			'CoreSetting' => array(
				'application_name' => 'Wasabi',
				'enable_caching' => '0',
				'cache_duration' => '30 days'
			)
		));
	}

	/**
	 * Migrate down
	 *
	 * @return void
	 */
	public function down() {
		$this->dropTable('settings');
	}

}
