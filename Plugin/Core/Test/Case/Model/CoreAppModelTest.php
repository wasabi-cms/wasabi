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
App::uses('CoreAppModel', 'Core.Model');

/**
 * @property CoreAppModel $CoreAppModel
 */

class CoreAppModelTest extends CakeTestCase {

	public function setUp() {
		$this->CoreAppModel = ClassRegistry::init('Core.CoreAppModel');

		parent::setUp();
	}

	public function testConfig() {
		$this->assertTrue(in_array('Containable', $this->CoreAppModel->actsAs));
		$this->assertEqual(-1, $this->CoreAppModel->recursive);
	}

	public function tearDown() {
		unset($this->CoreAppModel);

		parent::tearDown();
	}

}
