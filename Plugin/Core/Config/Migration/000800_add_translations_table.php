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

class AddTranslationsTable extends Migration {

	/**
	 * Migrate up
	 *
	 * @return void
	 */
	public function up() {
		$this->createTable('translations', array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'plugin' => array('type' => 'string', 'null' => false),
			'model' => array('type' => 'string', 'null' => false),
			'foreign_key' => array('type' => 'integer', 'null' => false),
			'language_id' => array('type' => 'integer', 'null' => false),
			'field' => array('type' => 'string', 'null' => false),
			'content' => array('type' => 'text', 'null' => false),
			'created' => array('type' => 'datetime', null => false),
			'modified' => array('type' => 'datetime', null => false),
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
		$this->dropTable('translations');
	}

}
