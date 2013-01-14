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
 * @subpackage    Wasabi.Plugin.Core.Test.Case.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AuthenticatorComponent', 'Core.Controller/Component');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeTestCase', 'TestSuite');
App::uses('ComponentCollection', 'Controller');
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');

class AuthenticatorTestComponent extends AuthenticatorComponent {

	public function getSettings() {
		return $this->_settings;
	}

	public function useSession() {
		return $this->_useSession();
	}

	public function useCookie() {
		return $this->_useCookie();
	}

	public function useGuestAccount() {
		return $this->_useGuestAccount();
	}

	public function getActiveUser() {
		return $this->_getActiveUser();
	}

	public function userModel() {
		return $this->_userModel;
	}

	public function getUserModel() {
		return $this->_getUserModel();
	}

	public function setUserModel($model) {
		$this->_userModel = $model;
	}

	public function getModelName() {
		return $this->_getModelName();
	}

	public function setSettings($settings) {
		$this->_settings = array_merge($this->_settings, $settings);
	}

}

class AuthenticatorTestController extends Controller {

	public $components = array(
		'AuthenticatorTest'
	);

}

class UserTestModel extends Model {

	public function authenticate($type = 'credentials', $credentials = null) {
		if ($credentials !== null && isset($credentials['token']) && $credentials['token'] === 'invalid_token') {
			return false;
		}
		if ($type === 'guest') {
			return 'guest';
		}
		return array(
			'UserTestModel' => array(
				'id' => 1,
				'group_id' => 1,
				'username' => 'admin',
			),
			'Group' => array(
				'id' => 1,
				'name' => 'Administrator'
			)
		);
	}

	public function persist($user, $duration) {
		return 'test_token';
	}

}

class UserTestModel2 extends Model {
}

class AuthenticatorComponentTest extends CakeTestCase {

	/**
	 * @var AuthenticatorTestComponent
	 */
	protected $Authenticator;

	/**
	 * @var AuthenticatorTestController
	 */
	protected $Controller;

	/**
	 * @var ComponentCollection
	 */
	protected $Collection;

	/**
	 * Test configuration settings
	 *
	 * @var array
	 */
	protected $_tSettings = array(
		'model' => 'UserTestModel',
		'sessionKey' => 'wasabi',
		'cookieKey' => 'wasabi'
	);

	protected $_tSession = array(
		'UserTestModel' => array(
			'id' => 1,
			'group_id' => 1,
			'username' => 'admin'
		),
		'Group' => array(
			'id' => 1,
			'name' => 'Administrator'
		)
	);

	public function setUp() {
		parent::setUp();

		$request = new CakeRequest(null, false);

		/**
		 * @var CakeResponse $response
		 */
		$response = $this->getMock('CakeResponse');
		$this->Controller = new AuthenticatorTestController($request, $response);
		$this->Controller->components['AuthenticatorTest'] = $this->_tSettings;

		$this->Collection = new ComponentCollection();
		$this->Collection->init($this->Controller);

		$this->Controller->Components->init($this->Controller);

		$this->Authenticator = $this->Controller->Components->load('AuthenticatorTest');
		$this->Authenticator->startup($this->Controller);
	}

	public function testRequiredComponentsAreLoaded() {
		$this->assertTrue(in_array('Session', $this->Authenticator->components));
		$this->assertTrue(in_array('Cookie', $this->Authenticator->components));
	}

	/**
	 * @dataProvider settings
	 */
	public function testConstructorThrowsException($msg, $model = null, $sK = null, $cK = null) {
		$this->setExpectedException('InvalidArgumentException', $msg);
		$this->Authenticator = new AuthenticatorTestComponent($this->Collection, array(
			'model' => $model,
			'sessionKey' => $sK,
			'cookieKey' => $cK
		));
	}

	public function testConstructorAppliesComponentSettings() {
		$expected = $this->_tSettings;
		$result = $this->Authenticator->getSettings();
		$this->assertEqual($expected, $result);
	}

	public function testGet() {
		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);
		$expected = array(
			'UserTestModel' => array(
				'id' => 1,
				'group_id' => 1,
				'username' => 'admin'
			),
			'Group' => array(
				'id' => 1,
				'name' => 'Administrator'
			)
		);

		$result = $this->Authenticator->get();
		$this->assertEqual($expected, $result);

		$expected = 1;
		$result = $this->Authenticator->get('id');
		$this->assertEqual($expected, $result);

		$result = $this->Authenticator->get('UserTestModel.id');
		$this->assertEqual($expected, $result);

		$expected = array(
			'id' => 1,
			'name' => 'Administrator'
		);
		$result = $this->Authenticator->get('Group');
		$this->assertEqual($expected, $result);

		$expected = 'Administrator';
		$result = $this->Authenticator->get('Group.name');
		$this->assertEqual($expected, $result);
	}

	public function testSet() {
		$this->assertFalse($this->Authenticator->set('id', 2));

		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);

		$this->assertTrue($this->Authenticator->set('UserTestModel.id', 2));
		$expected = 2;
		$result = $this->Authenticator->get('UserTestModel.id');
		$this->assertEqual($expected, $result);

		$this->assertTrue($this->Authenticator->set('Group.name', 'Foo'));
		$expected = 'Foo';
		$result = $this->Authenticator->get('Group.name');
		$this->assertEqual($expected, $result);

		$this->assertTrue($this->Authenticator->set('username', 'foo'));
		$expected = 'foo';
		$result = $this->Authenticator->get('username');
		$this->assertEqual($expected, $result);

		$this->assertTrue($this->Authenticator->set(array(
			'User.name' => 'foobar',
			'Group.name' => 'test'
		)));
		$expected = 'foobar';
		$result = $this->Authenticator->get('User.name');
		$this->assertEqual($expected, $result);
		$expected = 'test';
		$result = $this->Authenticator->get('Group.name');
		$this->assertEqual($expected, $result);
	}

	public function testDelete() {
		$this->assertFalse($this->Authenticator->delete('id'));

		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);

		$this->assertEqual(1, $this->Authenticator->get('id'));
		$this->assertTrue($this->Authenticator->delete('id'));
		$this->assertNull($this->Authenticator->get('id'));

		$this->assertEqual('Administrator', $this->Authenticator->get('Group.name'));
		$this->assertTrue($this->Authenticator->delete('Group.name'));
		$this->assertNull($this->Authenticator->get('Group.name'));

		$this->assertTrue($this->Authenticator->delete(array(
			'UserTestModel.group_id',
			'UserTestModel.username',
			'Group.id'
		)));
		$this->assertNull($this->Authenticator->get('UserTestModel.group_id'));
		$this->assertNull($this->Authenticator->get('UserTestModel.username'));
		$this->assertNull($this->Authenticator->get('Group.id'));
		$expected = array(
			'UserTestModel' => array(),
			'Group' => array()
		);
		$result = $this->Authenticator->get();
		$this->assertEqual($expected, $result);
	}

	public function testLoginThrowsException() {
		$this->Authenticator->setUserModel(ClassRegistry::init('UserTestModel2'));
		$this->setExpectedException('NotImplementedException', 'authenticate()');
		$this->Authenticator->login();
	}

	public function testLogin() {
		$this->Authenticator->setUserModel(ClassRegistry::init('UserTestModel'));
		$expected = $this->_tSession;
		$result = $this->Authenticator->login();
		$this->assertEqual($expected, $result);
		$this->assertEqual($expected, $this->Authenticator->Session->read($this->_tSettings['sessionKey']));
	}

	public function testLogout() {
		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);
		$this->Authenticator->Cookie->write($this->_tSettings['cookieKey'], '123', true, '2 weeks');
		$this->Authenticator->logout();
		$this->assertNull($this->Authenticator->Session->read($this->_tSettings['sessionKey']));
		$this->assertNull($this->Authenticator->Cookie->read($this->_tSettings['cookieKey']));
	}

	public function testPersistThrowsException() {
		$this->Authenticator->setUserModel(ClassRegistry::init('UserTestModel2'));
		$this->setExpectedException('NotImplementedException', 'persist()');
		$this->Authenticator->persist();
	}

	public function testPersist() {
		$this->assertNull($this->Authenticator->Cookie->read($this->_tSettings['cookieKey']));
		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);
		$this->assertTrue($this->Authenticator->persist('2 weeks'));
		$expected = 'test_token:2 weeks';
		$result = $this->Authenticator->Cookie->read($this->_tSettings['cookieKey']);
		$this->assertEqual($expected, $result);
	}

	public function testGetUserModel() {
		$this->assertNull($this->Authenticator->userModel());

		// first call automatically assigns Authenticator::$_userModel
		$expected = 'UserTestModel';
		$result = get_class($this->Authenticator->getUserModel());
		$this->assertEqual($expected, $result);

		// second call checks for plain return of the now already initialized user model
		$result = get_class($this->Authenticator->getUserModel());
		$this->assertEqual($expected, $result);
	}

	public function testGetActiveUser() {
		$expected = 'guest';
		$this->assertEqual($expected, $this->Authenticator->getActiveUser());

		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);
		$expected = $this->_tSession;
		$this->assertEqual($expected, $this->Authenticator->getActiveUser());

		$this->Authenticator->Session->delete($this->_tSettings['sessionKey']);
		$this->Authenticator->Cookie->write($this->_tSettings['cookieKey'], 'test_token:2 weeks');
		$expected = $this->_tSession;
		$this->assertEqual($expected, $this->Authenticator->getActiveUser());
	}

	public function testUseSession() {
		$this->assertFalse($this->Authenticator->useSession());

		$this->Authenticator->Session->startup($this->Controller);
		$this->Authenticator->Session->write($this->_tSettings['sessionKey'], $this->_tSession);
		$this->assertTrue($this->Authenticator->useSession());
	}

	public function testUseCookie() {
		$this->assertFalse($this->Authenticator->useCookie());

		$this->Authenticator->Cookie->write($this->_tSettings['cookieKey'], 'temp_token:2 weeks');
		$this->assertTrue($this->Authenticator->useCookie());
		$expected = 'test_token:2 weeks';
		$result = $this->Authenticator->Cookie->read($this->_tSettings['cookieKey']);
		$this->assertEqual($expected, $result);

		$this->Authenticator->Cookie->write($this->_tSettings['cookieKey'], 'invalid_token');
		$this->assertFalse($this->Authenticator->useCookie());
		$this->assertNull($this->Authenticator->Cookie->read($this->_tSettings['cookieKey']));

		$this->Authenticator->Cookie->write($this->_tSettings['cookieKey'], 'invalid_token:2 weeks');
		$this->assertFalse($this->Authenticator->useCookie());
		$this->assertNull($this->Authenticator->Cookie->read($this->_tSettings['cookieKey']));
	}

	public function testUseGuest() {
		$expected = 'guest';
		$result = $this->Authenticator->useGuestAccount();
		$this->assertEqual($expected, $result);
	}

	public function testGetModelName() {
		$expected = 'UserTestModel';

		$result = $this->Authenticator->getModelName();
		$this->assertEqual($expected, $result);

		$this->Authenticator->setSettings(array('model' => 'TestPlugin.FooBar'));
		$expected = 'FooBar';
		$result = $this->Authenticator->getModelName();
		$this->assertEqual($expected, $result);
	}

	public function tearDown() {
		parent::tearDown();

		$this->Authenticator->Session->delete($this->_tSettings['sessionKey']);
		$this->Authenticator->Cookie->delete($this->_tSettings['cookieKey']);
		unset($this->Authenticator);
		unset($this->Collection);
		unset($this->Controller);
	}

	public function settings() {
		return array(
			array('model'      ,null        ,null     ,null),
			array('sessionKey' ,'Core.User' ,null     ,null),
			array('cookieKey'  ,'Core.User' ,'wasabi' ,null)
		);
	}

}