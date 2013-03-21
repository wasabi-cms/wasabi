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
 * @subpackage    Wasabi.Plugin.Core.Test.TestSuite
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeSession', 'Model/Datasource');
App::uses('ClassRegistry', 'Utility');
App::uses('ControllerTestCase', 'TestSuite');

class CoreControllerTest extends ControllerTestCase {

	/**
	 * Holds the backend url prefix.
	 *
	 * @var string
	 */
	public $backendPrefix;

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		$this->backendPrefix = Configure::read('Wasabi.backend_prefix');

		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->backendPrefix);

		CakeSession::clear();
		CakeSession::destroy();
		ClassRegistry::flush();

		parent::tearDown();
	}

	/**
	 * Login a user via Session.
	 * This method should be used for controller tests that require a logged in user.
	 *
	 * @param bool|integer $fake_id
	 */
	protected function _loginUser($fake_id = false) {
		CakeSession::write('wasabi', array(
			'User' => array(
				'id' => ($fake_id !== false) ? (int) $fake_id : 1,
				'username' => 'admin'
			)
		));
	}

	/**
	 * Force an ajax request.
	 * The $_SERVER variable is reset before every test, so this is safe
	 *
	 * @see http://stackoverflow.com/questions/8182278/testing-ajax-request-in-cakephp-2-0
	 */
	protected function _makeAjax() {
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
	}

}