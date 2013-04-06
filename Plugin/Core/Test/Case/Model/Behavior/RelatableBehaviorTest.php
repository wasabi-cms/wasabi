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

App::uses('CakePlugin', 'Core');
App::uses('CakeTestCase', 'TestSuite');
App::uses('ClassRegistry', 'Utility');
App::uses('Model', 'Model');
App::uses('TestPluginPost', 'TestPlugin.Model');
App::uses('RelatableBehavior', 'Core.Model/Behavior');

/**
 * @property TestPluginPost $TestPluginPost
 */
class RelatableBehaviorTest extends CakeTestCase {

	public $fixtures = array('plugin.core.test_plugin_author', 'plugin.core.test_plugin_article', 'plugin.core.test_plugin_post', 'plugin.core.test_plugin_comment', 'plugin.core.test_plugin_author_group', 'plugin.core.test_plugin_category');

	public function setUp() {
		App::build(array(
			'Plugin' => array(CakePlugin::path('Core') . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS),
		), APP::RESET);

		CakePlugin::load('TestPlugin');

		$this->TestPluginPost = ClassRegistry::init('TestPlugin.TestPluginPost');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->TestPluginPost);

		parent::tearDown();
	}

	public function testBelongsTo() {
		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor'
			),
			'limit' => 2
		));
		$this->assertEqual(2, count($result));
		$this->assertTrue(array_key_exists('TestPluginPost', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthor', $result[0]));
		foreach ($result as $r) {
			$this->assertEqual($r['TestPluginPost']['test_plugin_author_id'], $r['TestPluginAuthor']['id']);
		}

		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'conditions' => array(
						'TestPluginAuthor.name' => 'Kim'
					)
				)
			)
		));
		$this->assertEqual(2, count($result));
		$this->assertTrue(array_key_exists('TestPluginPost', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthor', $result[0]));
		$this->assertEqual('Kim', $result[1]['TestPluginAuthor']['name']);
		$this->assertEqual('Kim', $result[1]['TestPluginAuthor']['name']);

		$result = $this->TestPluginPost->find('first', array(
			'related' => array(
				'TestPluginAuthor'
			),
			'conditions' => array(
				'TestPluginPost.id' => 3
			)
		));
		$this->assertEqual('Post 3 by Jim', $result['TestPluginPost']['title']);
		$this->assertEqual('Jim', $result['TestPluginAuthor']['name']);

		$result = $this->TestPluginPost->find('count', array(
			'related' => array(
				'TestPluginAuthor'
			),
			'conditions' => array(
				'TestPluginAuthor.name' => 'Jim'
			)
		));
		$this->assertEqual(3, $result);

		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'TestPluginAuthorGroup'
				)
			)
		));
		$this->assertTrue(array_key_exists('TestPluginPost', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthor', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthorGroup', $result[0]));
	}

	public function testBelongsToDeep() {
		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'TestPluginAuthorGroup'
				)
			),
			'limit' => 2
		));
		$this->assertEqual(2, count($result));
		$this->assertTrue(array_key_exists('TestPluginPost', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthor', $result[0]));
		$this->assertTrue(array_key_exists('TestPluginAuthorGroup', $result[0]));

		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'TestPluginAuthorGroup'
				)
			),
			'conditions' => array(
				'TestPluginAuthorGroup.name' => 'CakePHP Experts'
			)
		));
		$this->assertEqual(5, count($result));

		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'TestPluginAuthorGroup'
				)
			),
			'conditions' => array(
				'TestPluginAuthorGroup.name' => 'jQuery Experts'
			)
		));
		$this->assertEqual(1, count($result));
	}

	public function testBelongsToFields() {
		$result = $this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'TestPluginAuthorGroup'
				)
			),
			'fields' => array(
				'TestPluginPost.*',
				'TestPluginAuthor.name',
				'TestPluginAuthorGroup.name'
			),
			'limit' => 2
		));
		$this->assertEqual(2, count($result));
		foreach ($result as $r) {
			$this->assertTrue(isset($r['TestPluginAuthor']['id']));
			$this->assertTrue(isset($r['TestPluginAuthor']['name']));
			$this->assertTrue(isset($r['TestPluginAuthorGroup']['id']));
			$this->assertTrue(isset($r['TestPluginAuthorGroup']['name']));
			$this->assertFalse(isset($r['TestPluginAuthor']['email']));
			$this->assertFalse(isset($r['TestPluginAuthor']['test_plugin_author_group_id']));
		}
	}

	public function testHasMany() {
		$result = $this->TestPluginPost->TestPluginAuthor->find('all', array(
			'related' => array(
				'TestPluginPost'
			)
		));
		$this->assertEqual(3, count($result));
		foreach ($result as $r) {
			$this->assertTrue(array_key_exists('TestPluginAuthor', $r));
			$this->assertTrue(array_key_exists('TestPluginPosts', $r['TestPluginAuthor']));
		}
		$this->assertEqual(3, count($result[0]['TestPluginAuthor']['TestPluginPosts']));
		$this->assertEqual(2, count($result[1]['TestPluginAuthor']['TestPluginPosts']));
		$this->assertEqual(1, count($result[2]['TestPluginAuthor']['TestPluginPosts']));

		$result = $this->TestPluginPost->TestPluginAuthor->find('all', array(
			'related' => array(
				'TestPluginPost' => array(
					'TestPluginCategory'
				)
			)
		));
		$this->assertEqual(3, count($result));
		foreach ($result as $r) {
			$this->assertTrue(array_key_exists('TestPluginAuthor', $r));
			$this->assertTrue(array_key_exists('TestPluginPosts', $r['TestPluginAuthor']));
			foreach ($r['TestPluginAuthor']['TestPluginPosts'] as $post) {
				$this->assertTrue(array_key_exists('TestPluginPost', $post));
				$this->assertTrue(array_key_exists('TestPluginCategory', $post));
			}
		}

		$result = $this->TestPluginPost->TestPluginAuthor->find('all', array(
			'related' => array(
				'TestPluginPost' => array(
					'TestPluginCategory',
					'TestPluginComment'
				)
			)
		));
		$this->assertEqual(3, count($result));
		foreach ($result as $r) {
			$this->assertTrue(array_key_exists('TestPluginAuthor', $r));
			$this->assertTrue(array_key_exists('TestPluginPosts', $r['TestPluginAuthor']));
			foreach ($r['TestPluginAuthor']['TestPluginPosts'] as $post) {
				$this->assertTrue(array_key_exists('TestPluginPost', $post));
				$this->assertTrue(array_key_exists('TestPluginCategory', $post));
				$this->assertTrue(array_key_exists('TestPluginComments', $post['TestPluginPost']));
			}
		}
		$this->assertEqual(2, count($result[0]['TestPluginAuthor']['TestPluginPosts'][0]['TestPluginPost']['TestPluginComments']));

		$result = $this->TestPluginPost->TestPluginAuthor->find('all', array(
			'related' => array(
				'TestPluginPost' => array(
					'TestPluginCategory' => array(
						'TestPluginArticle',
						'TestPluginPost'
					),
					'TestPluginComment'
				),
				'TestPluginAuthorGroup' => array(
					'TestPluginAuthor'
				)
			)
		));
		$this->assertEqual(3, count($result));
		foreach ($result as $r) {
			$this->assertTrue(array_key_exists('TestPluginAuthor', $r));
			$this->assertTrue(array_key_exists('TestPluginPosts', $r['TestPluginAuthor']));
			$this->assertTrue(array_key_exists('TestPluginAuthorGroup', $r));
			$this->assertTrue(array_key_exists('TestPluginAuthors', $r['TestPluginAuthorGroup']));
			foreach ($r['TestPluginAuthor']['TestPluginPosts'] as $post) {
				$this->assertTrue(array_key_exists('TestPluginPost', $post));
				$this->assertTrue(array_key_exists('TestPluginComments', $post['TestPluginPost']));
				$this->assertTrue(array_key_exists('TestPluginCategory', $post));
				$this->assertTrue(array_key_exists('TestPluginArticles', $post['TestPluginCategory']));
				foreach ($post['TestPluginPost']['TestPluginComments'] as $comment) {
					$this->assertTrue(array_key_exists('TestPluginComment', $comment));
				}
				foreach ($post['TestPluginCategory']['TestPluginArticles'] as $article) {
					$this->assertTrue(array_key_exists('TestPluginArticle', $article));
				}
			}
			foreach ($r['TestPluginAuthorGroup']['TestPluginAuthors'] as $author) {
				$this->assertTrue(array_key_exists('TestPluginAuthor', $author));
			}
		}
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Model "TestPluginPost" is not associated with model "Foo"
	 */
	public function testThrowWarning() {
		$this->TestPluginPost->find('all', array(
			'related' => array(
				'Foo'
			)
		));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Model "TestPluginAuthor" is not associated with model "Profile"
	 */
	public function testThrowWarningDeep() {
		$this->TestPluginPost->find('all', array(
			'related' => array(
				'TestPluginAuthor' => array(
					'Profile'
				),
				'TestPluginComment'
			)
		));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 * @expectedExceptionMessage Model "TestPluginCategory" is not associated with model "Test"
	 */
	public function testThrowWarningDeep2() {
		$this->TestPluginPost->TestPluginAuthor->find('all', array(
			'related' => array(
				'TestPluginPost' => array(
					'TestPluginCategory' => array(
						'Test'
					),
					'TestPluginComment'
				)
			)
		));
	}
}
