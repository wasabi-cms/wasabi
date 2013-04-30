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
App::uses('Language', 'Core.Model');
App::uses('Migration', 'Migrations.Model');

class AddLanguagesTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('languages', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'name' => array('type' => 'string', 'null' => false),
			'locale' => array('type' => 'string', 'length' => 2, 'null' => false),
			'iso' => array('type' => 'string', 'length' => 3, 'null' => false),
			'lang' => array('type' => 'string', 'length' => 5, 'null' => false),
			'available_at_frontend' => array('type' => 'boolean', 'null' => false, 'default' => 0),
			'available_at_backend' => array('type' => 'boolean', 'null' => false, 'default' => 0),
			'position' => array('type' => 'integer', 'null' => true, 'default' => null),
			'created' => array('type' => 'datetime', 'null' => false),
			'modified' => array('type' => 'datetime', 'null' => false),
			'indexes' => array(
				'PRIMARY' => array(
					'column' => 'id',
					'unique' => 1
				)
			)
		));

		$language = ClassRegistry::init('Core.Language');
		// English
		$language->create(array(
			'name' => 'English',
			'locale' => 'en',
			'iso' => 'eng',
			'lang' => 'en-US',
			'available_at_frontend' => true,
			'available_at_backend' => true,
			'position' => 1
		));
		$language->save();
		// Deutsch
		$language->create(array(
			'name' => 'Deutsch',
			'locale' => 'de',
			'iso' => 'deu',
			'lang' => 'de-DE',
			'available_at_frontend' => false,
			'available_at_backend' => true,
			'position' => 2
		));
		$language->save();
	}

	/**
	 * Migrate down
	 *
	 * @return void
	 */
	public function down() {
		$this->dropTable('languages');
	}

}
