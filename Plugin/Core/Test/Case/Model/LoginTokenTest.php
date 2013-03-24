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
 * @subpackage    Wasabi.Plugin.Core.Test.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeTestCase', 'TestSuite');
App::uses('ClassRegistry', 'Utility');

/**
 * @property LoginToken $LoginToken
 */

class LoginTokenTest extends CakeTestCase {

	public $fixtures = array('plugin.core.login_token', 'plugin.core.user');

	public function setUp() {
		$this->LoginToken = ClassRegistry::init('Core.LoginToken');

		parent::setUp();
	}

	public function tearDown() {
		unset($this->LoginToken);

		parent::tearDown();
	}

	public function testBelongsToUser() {
		$this->assertTrue(array_key_exists('User', $this->LoginToken->belongsTo));
	}

	public function testFindActiveToken() {
		$this->assertNotEmpty($this->LoginToken->findActiveToken('i_am_a_very_secret_token'));
	}

	public function testAdd() {
		$token_count = $this->LoginToken->find('count');

		$this->assertFalse($this->LoginToken->hasAny(array('user_id' => 99)));
		$this->assertNotEmpty($this->LoginToken->add(99, 'yoyo', '2 weeks'));
		$this->assertEqual(($token_count + 1), $this->LoginToken->find('count'));
		$this->assertTrue($this->LoginToken->hasAny(array('user_id' => 99)));
	}

	public function testAlreadyExists() {
		$this->assertTrue($this->LoginToken->alreadyExists('i_am_a_very_secret_token'));
		$this->assertFalse($this->LoginToken->alreadyExists('new_token'));
	}

	public function testDeleteExpiredTokens() {
		$this->assertEqual(1, $this->LoginToken->find('count'));

		$this->LoginToken->id = 1;
		$this->LoginToken->saveField('expires', date('Y-m-d H:i:s', strtotime('-2 weeks')));

		$this->assertTrue($this->LoginToken->deleteExpiredTokens());
		$this->assertEqual(0, $this->LoginToken->find('count'));
	}

}
