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

	public function testFindAll() {
		$result = $this->Language->findAll();
		$expected = array(
			array(
				'Language' => array(
					'id' => 1,
					'name' => 'English',
					'locale' => 'en',
					'iso' => 'eng',
					'lang' => 'en-US',
					'available_at_frontend' => true,
					'available_at_backend' => true,
					'position' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			),
			array(
				'Language' => array(
					'id' => 2,
					'name' => 'Deutsch',
					'locale' => 'de',
					'iso' => 'deu',
					'lang' => 'de-DE',
					'available_at_frontend' => false,
					'available_at_backend' => true,
					'position' => 2,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->Language->findAll(array('conditions' => array('id' => 1)));
		$expected = array(
			array(
				'Language' => array(
					'id' => 1,
					'name' => 'English',
					'locale' => 'en',
					'iso' => 'eng',
					'lang' => 'en-US',
					'available_at_frontend' => true,
					'available_at_backend' => true,
					'position' => 1,
					'created' => '2013-01-12 14:00:00',
					'modified' => '2013-01-12 14:00:00'
				)
			)
		);
		$this->assertEqual($expected, $result);
	}

	public function testFindById() {
		$result = $this->Language->findById(1);
		$expected = array(
			'Language' => array(
				'id' => 1,
				'name' => 'English',
				'locale' => 'en',
				'iso' => 'eng',
				'lang' => 'en-US',
				'available_at_frontend' => true,
				'available_at_backend' => true,
				'position' => 1,
				'created' => '2013-01-12 14:00:00',
				'modified' => '2013-01-12 14:00:00'
			)
		);
		$this->assertEqual($expected, $result);

		$result = $this->Language->findById(100);
		$this->assertEmpty($result);
	}

	public function tearDown() {
		unset($this->Language);

		parent::tearDown();
	}

}
