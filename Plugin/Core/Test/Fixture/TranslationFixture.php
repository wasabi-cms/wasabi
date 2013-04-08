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
 * @subpackage    Wasabi.Plugin.Core.Test.Fixture
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestFixture', 'TestSuite/Fixture');

class TranslationFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'plugin' => array('type' => 'string', 'length' => 255, 'null' => false),
		'model' => array('type' => 'string', 'length' => 255, 'null' => false),
		'foreign_key' => array('type' => 'integer', 'null' => false),
		'language_id' => array('type' => 'integer', 'null' => false),
		'field' => array('type' => 'string', 'length' => 255),
		'content' => array('type' => 'text', 'null' => false),
		'created' => 'datetime',
		'modified' => 'datetime'
	);

	public $records;

	public function init() {
		$this->records = array(
			// title
			array(
				'id' => 1,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 1,
				'language_id' => 1,
				'field' => 'title',
				'content' => 'Title 1'
			),
			array(
				'id' => 2,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 1,
				'language_id' => 2,
				'field' => 'title',
				'content' => 'Titel 1'
			),
			array(
				'id' => 3,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 3,
				'language_id' => 1,
				'field' => 'title',
				'content' => 'Title 3'
			),
			array(
				'id' => 4,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 3,
				'language_id' => 2,
				'field' => 'title',
				'content' => 'Titel 3'
			),
			array(
				'id' => 5,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 4,
				'language_id' => 1,
				'field' => 'title',
				'content' => 'Title 4'
			),
			array(
				'id' => 6,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 4,
				'language_id' => 2,
				'field' => 'title',
				'content' => 'Titel 4'
			),
			// content
			array(
				'id' => 7,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 1,
				'language_id' => 1,
				'field' => 'content',
				'content' => 'Content 1'
			),
			array(
				'id' => 8,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 1,
				'language_id' => 2,
				'field' => 'content',
				'content' => 'Inhalt 1'
			),
			array(
				'id' => 9,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 3,
				'language_id' => 1,
				'field' => 'content',
				'content' => 'Content 3'
			),
			array(
				'id' => 10,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 3,
				'language_id' => 2,
				'field' => 'content',
				'content' => 'Inhalt 3'
			),
			array(
				'id' => 11,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 4,
				'language_id' => 1,
				'field' => 'content',
				'content' => 'Content 4'
			),
			array(
				'id' => 12,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginArticle',
				'foreign_key' => 4,
				'language_id' => 2,
				'field' => 'content',
				'content' => 'Inhalt 4'
			),

			// Categories
			array(
				'id' => 13,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginCategory',
				'foreign_key' => 1,
				'language_id' => 1,
				'field' => 'name',
				'content' => 'Category 1'
			),
			array(
				'id' => 14,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginCategory',
				'foreign_key' => 1,
				'language_id' => 2,
				'field' => 'name',
				'content' => 'Kategorie 1'
			),

			// Posts
			array(
				'id' => 15,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginPost',
				'foreign_key' => 1,
				'language_id' => 1,
				'field' => 'title',
				'content' => 'Post 1 by Jim'
			),
			array(
				'id' => 16,
				'plugin' => 'TestPlugin',
				'model' => 'TestPluginPost',
				'foreign_key' => 1,
				'language_id' => 2,
				'field' => 'title',
				'content' => 'Beitrag 1 von Jim'
			),
		);
		parent::init();
	}

}
