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
App::uses('GroupPermission', 'Core.Model');

/**
 * @property GroupPermission $GroupPermission
 */

class GroupPermissionTest extends CakeTestCase {

	public function setUp() {
		$this->GroupPermission = ClassRegistry::init('Core.GroupPermission');

		parent::setUp();
	}

	public function testGroupPermissionBelongsToGroup() {
		$this->assertTrue(array_key_exists('Group', $this->GroupPermission->belongsTo));
	}

	public function tearDown() {
		unset($this->GroupPermission);

		parent::tearDown();
	}

}
