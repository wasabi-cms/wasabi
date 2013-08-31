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
App::uses('CoreSetting', 'Core.Model');

/**
 * @property CoreSetting $CoreSetting
 */

class CoreSettingTest extends CakeTestCase {

	public $fixtures = array('plugin.core.setting');

	public function setUp() {
		$this->CoreSetting = ClassRegistry::init('Core.CoreSetting');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->CoreSetting);

		parent::tearDown();
	}

	public function testNoTestCasesYet() {}

}
