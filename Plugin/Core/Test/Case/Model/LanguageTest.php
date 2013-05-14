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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeSession', 'Model/Datasource');
App::uses('CakeTestCase', 'TestSuite');
App::uses('ClassRegistry', 'Utility');
App::uses('Language', 'Core.Model');

/**
 * @property Language $Language
 */

class LanguageTest extends CakeTestCase {

	public $fixtures = array('plugin.core.language');

	public function setUp() {
		$this->Language = ClassRegistry::init('Core.Language');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->Language);
		Cache::delete('languages', 'core.infinite');

		parent::tearDown();
	}

	public function testFindAll() {
		$result = $this->Language->findAll();
		$this->assertNotEmpty($result);
		$this->assertEqual(3, count($result));

		$result = $this->Language->findAll(array('conditions' => array('id' => 1)));
		$this->assertNotEmpty($result);
		$this->assertEqual(1, count($result));
	}

	public function testFindById() {
		$result = $this->Language->findById(1);
		$this->assertNotEmpty($result);
		$this->assertEqual(1, count($result));

		$result = $this->Language->findById(100);
		$this->assertEmpty($result);
	}

	public function testAfterSave() {
		Cache::write('languages', 'test', 'core.infinite');
		$this->Language->afterSave(false);
		$this->assertFalse(Cache::read('languages', 'core.infinite'));

		Cache::write('languages', 'test', 'core.infinite');
		$this->Language->afterSave(true);
		$this->assertFalse(Cache::read('languages', 'core.infinite'));
	}

	public function testCanBeDeleted() {
		$this->assertFalse($this->Language->canBeDeleted(1));
		$this->assertFalse($this->Language->canBeDeleted(2));
		$this->assertFalse($this->Language->canBeDeleted(99));

		$this->assertFalse($this->Language->canBeDeleted(4));
		$this->Language->save(array(
			'Language' => array(
				'name' => 'Test Lang',
				'locale' => 'tl',
				'iso' => 'tla',
				'lang' => 'tl-LA',
				'available_at_backend' => false,
				'available_at_frontend' => false
			)
		));
		$this->assertTrue($this->Language->canBeDeleted(3));
	}

	public function testAfterDelete1() {
		CakeSession::write('Wasabi.content_language_id', 2);
		Cache::write('languages', 'blub', 'core.infinite');

		$this->Language->delete(3);
		$this->assertEquals(2, CakeSession::read('Wasabi.content_language_id'));
		$this->assertFalse(Cache::read('languages', 'core.infinite'));
	}

	public function testAfterDelete2() {
		$this->Language->save(array(
			'id' => 3,
			'available_at_frontend' => true
		));
		CakeSession::write('Wasabi.content_language_id', 3);
		$this->Language->delete(3);
		$this->assertFalse(CakeSession::check('Wasabi.content_language_id'));
	}

	public function testAfterDelete3() {
		$this->Language->save(array(
			'id' => 3,
			'available_at_frontend' => true
		));
		$this->Language->save(array(
			'id' => 1,
			'available_at_frontend' => false
		));

		$this->Language->delete(3);
		$result = $this->Language->field('available_at_frontend', array('id' => 1));
		$this->assertTrue($result);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAtLeastOneFrontendLanguageIsAvailableThrowsException() {
		$this->Language->atLeastOneFrontendLanguageIsAvailable(null, false);
	}

	public function testAtLeastOneFrontendLanguageIsAvailable() {
		$this->assertTrue($this->Language->atLeastOneFrontendLanguageIsAvailable(null, false, 3));
		$this->assertFalse($this->Language->atLeastOneFrontendLanguageIsAvailable(null, false, 1));

		$this->Language->data = array(
			'Language' => array(
				'id' => 1
			)
		);
		$check = array('available_at_frontend' => 0);
		$this->assertFalse($this->Language->atLeastOneFrontendLanguageIsAvailable($check));
	}

}
