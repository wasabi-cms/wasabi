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

}
