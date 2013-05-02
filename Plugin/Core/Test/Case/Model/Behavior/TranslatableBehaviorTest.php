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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestCase', 'TestSuite');
App::uses('TranslatableBehavior', 'Core.Model/Behavior');
App::uses('TestPluginArticle', 'TestPlugin.Model');

/**
 * @property TestPluginArticle $Article
 */
class TranslatableBehaviorTest extends CakeTestCase {

	public $fixtures = array('plugin.core.language', 'plugin.core.translation', 'plugin.core.test_plugin_article', 'plugin.core.test_plugin_category', 'plugin.core.test_plugin_post');

	public function setUp() {
		Configure::write('Languages', array(
			'frontend' => array(
				array('id' => 1, 'locale' => 'en'),
				array('id' => 2, 'locale' => 'de')
			)
		));

		App::build(array(
			'Plugin' => array(CakePlugin::path('Core') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
		), APP::RESET);

		CakePlugin::load('TestPlugin');

		$this->Article = ClassRegistry::init('TestPlugin.TestPluginArticle');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Article);

		CakePlugin::unload('TestPlugin');

		parent::tearDown();
	}

	public function testFindCountWithoutContentLanguage() {
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Title 1'
			)
		));
		$this->assertEqual(1, $result);

		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Titel 1'
			)
		));
		$this->assertEqual(0, $result);

		$result = $this->Article->find('count', array(
			'conditions' => array(
				'content' => 'Content 1'
			)
		));
		$this->assertEqual(1, $result);

		$result = $this->Article->find('count', array(
			'conditions' => array(
				'content' => 'Inhalt 1'
			)
		));
		$this->assertEqual(0, $result);
	}

	public function testFindCountWithContentLanguage() {
		Configure::write('Wasabi.content_language', array('id' => 1));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Title 1',
			)
		));
		$this->assertEqual(1, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Titel 1',
			)
		));
		$this->assertEqual(1, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'subtitle' => 'Subtitle'
			)
		));
		$this->assertEqual(2, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Titel 4',
				'content' => 'Content 4',
				'subtitle' => 'Subtitle 4'
			)
		));
		$this->assertEqual(0, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Titel 4',
				'content' => 'Inhalt 4',
				'subtitle' => 'Subtitle 4'
			)
		));
		$this->assertEqual(1, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'TestPluginArticle.title' => 'Titel 4',
				'TestPluginArticle.content' => 'Inhalt 4',
				'TestPluginArticle.subtitle' => 'Subtitle 4'
			)
		));
		$this->assertEqual(1, $result);

		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('count', array(
			'conditions' => array(
				'title' => 'Titel 4',
				'content' => 'Inhalt 4',
				'TestPluginArticle.subtitle' => 'Subtitle 4'
			)
		));
		$this->assertEqual(1, $result);
	}

	public function testFindAllWithLanguage() {
		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('all');
		$this->assertEqual('Titel 1', $result[0]['TestPluginArticle']['title']);
		$this->assertEqual('Inhalt 1', $result[0]['TestPluginArticle']['content']);
		// article with id 2 has no translations
		$this->assertEqual('Title 2', $result[1]['TestPluginArticle']['title']);
		$this->assertEqual('Content 2', $result[1]['TestPluginArticle']['content']);
		$this->assertEqual('Titel 3', $result[2]['TestPluginArticle']['title']);
		$this->assertEqual('Inhalt 3', $result[2]['TestPluginArticle']['content']);
		$this->assertEqual('Titel 4', $result[3]['TestPluginArticle']['title']);
		$this->assertEqual('Inhalt 4', $result[3]['TestPluginArticle']['content']);
	}

	public function testFindAllRelatedWithLanguage() {
		Configure::write('Wasabi.content_language', array('id' => 2));
		$result = $this->Article->find('all', array(
			'related' => array(
				'TestPluginCategory' => array(
					'TestPluginPost'
				)
			),
			'order' => 'TestPluginArticle.id ASC'
		));
		$this->assertEqual(4, count($result));
		foreach ($result as $r) {
			$this->assertTrue(array_key_exists('TestPluginArticle', $r));
			$this->assertTrue(array_key_exists('TestPluginCategory', $r));
			$this->assertTrue(array_key_exists('TestPluginPosts', $r['TestPluginCategory']));
		}
		$this->assertEqual('Titel 1', $result[0]['TestPluginArticle']['title']);
		$this->assertEqual('Inhalt 1', $result[0]['TestPluginArticle']['content']);
		$this->assertEqual('Kategorie 1', $result[0]['TestPluginCategory']['name']);
		$this->assertEqual('Beitrag 1 von Jim', $result[0]['TestPluginCategory']['TestPluginPosts'][0]['TestPluginPost']['title']);
	}

}
