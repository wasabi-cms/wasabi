<?php
/**
 * Authenticator component handles user authentication
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');
App::uses('Hash', 'Utility');

/**
 * @property CookieComponent $Cookie
 * @property SessionComponent $Session
 */

class AuthenticatorComponent extends Component {

	/**
	 * Components used by this component
	 *
	 * @var array
	 */
	public $components = array(
		'Session',
		'Cookie'
		/*'RequestHandler'*/
	);

	/**
	 * Default setting keys
	 * require initialization through constructor
	 *
	 * @var array
	 */
	protected $_settings = array(
		'model' => null,
		'sessionKey' => 'Auth',
		'cookieName' => 'remember',
		'cookieKey' => 'me',
		'rememberFor' => '+2 weeks'
	);

	/**
	 * Holds the User Model instance
	 *
	 * @var object
	 */
	protected $_userModel;

	/**
	 * Constructor
	 *
	 * @param ComponentCollection $collection
	 * @param array $settings
	 * @throws InvalidArgumentException
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->_Collection = $collection;
		$this->_settings = Hash::merge($this->_settings, $settings);

		if ($this->_settings['model'] === null) {
			throw new InvalidArgumentException('You must use a valid authentication model like "YourPluginName.User".');
		}

		if ($this->_settings['sessionKey'] === null) {
			throw new InvalidArgumentException('You must supply a sessionKey in the settings.');
		}

		if ($this->_settings['cookieKey'] === null) {
			throw new InvalidArgumentException('You must supply a cookieKey in the settings.');
		}

		if (!empty($this->components)) {
			$this->_componentMap = ComponentCollection::normalizeObjectArray($this->components);
		}

		$this->Cookie->name = $this->_settings['cookieName'];

		new Authenticator($this);
	}

	/**
	 * Return the active user
	 * or a specific field value from the active user (e.g. if $field = 'id')
	 * or a dot notated field value from its related models (e.g. if $field = 'Group.id')
	 *
	 * @param null|string $field
	 * @return mixed
	 */
	public function get($field = null) {
		$user = $this->_getActiveUser();
		if ($field === null) {
			return $user;
		}
		if (strpos($field, '.') === false) {
			$model = $this->_getModelName();
			if (in_array($field, array_keys($user[$model]))) {
				return $user[$model][$field];
			}
		}
		if (!$user) {
			return false;
		}
		return Hash::get($user, $field);
	}

	/**
	 * Set a specific field value for the active user
	 * or multiple field values if $fields is an array.
	 * Uses the same field notation as the get() method.
	 *
	 * @param array|string $fields
	 * @param mixed $value
	 * @return boolean
	 */
	public function set($fields, $value = null) {
		$user = $this->Session->read($this->_settings['sessionKey']);
		if (empty($user)) {
			return false;
		}
		if (!is_array($fields) && $value !== null) {
			$fields = array($fields => $value);
		}
		foreach ($fields as $field => $value) {
			if (strpos($field, '.') === false) {
				$user[$this->_getModelName()][$field] = $value;
			} else {
				$user = Hash::insert($user, $field, $value);
			}
		}
		return $this->Session->write($this->_settings['sessionKey'], $user);
	}

	/**
	 * Delete a specific field of the active user
	 * or multiple fields if $fields is an array.
	 * Uses the same field notation as the get() method.
	 *
	 * @param array|string $fields
	 * @return bool
	 */
	public function delete($fields) {
		$user = $this->Session->read($this->_settings['sessionKey']);
		if (empty($user)) {
			return false;
		}
		if (!is_array($fields)) {
			$fields = array($fields);
		}
		foreach ($fields as $field) {
			if (strpos($field, '.') !== false) {
				$user = Hash::remove($user, $field);
			} elseif (isset($user[$this->_getModelName()][$field])) {
				unset($user[$this->_getModelName()][$field]);
			}
		}
		$this->Session->write($this->_settings['sessionKey'], $user);
		return true;
	}

	/**
	 * Login function checks for authenticate() method on the user model
	 * and calls it to retrieve an authenticated user.
	 * If authentication succeeds the user is stored in the session.
	 *
	 * @param string $type
	 * @param array|null|string $credentials
	 * @throws NotImplementedException
	 * @return mixed
	 */
	public function login($type = 'credentials', $credentials = null) {
		$userModel = $this->_getUserModel();
		if (!method_exists($userModel, 'authenticate')) {
			throw new NotImplementedException($userModel->alias . '::authenticate() is not implemented!');
		}
		$user = $userModel->authenticate($type, $credentials);
		$this->Session->write($this->_settings['sessionKey'], $user);
		return $user;
	}

	/**
	 * Logout a user and destroy his Cookie and Session.
	 *
	 * @return bool
	 */
	public function logout() {
		$this->Cookie->delete($this->_settings['cookieKey']);
		$this->Session->delete($this->_settings['sessionKey']);
		return true;
	}

	/**
	 * Persist a user login for $duration by calling the persist() method on the user model
	 * that returns a token, which is then stored in a cookie.
	 *
	 * @throws NotImplementedException
	 * @return boolean
	 */
	public function persist() {
		$userModel = $this->_getUserModel();
		if (!method_exists($userModel, 'persist')) {
			throw new NotImplementedException($userModel->alias . '::persist() is not implemented!');
		}
		$userId = $this->get('id');
		$duration = $this->_settings['rememberFor'];
		$token = $userModel->persist($userId, $duration);
		$this->Cookie->write($this->_settings['cookieKey'], $token, true /* encrypt */, $duration);
		return true;
	}

	/**
	 * Setter/Getter of a User model instance
	 *
	 * @return object
	 */
	protected function _getUserModel() {
		if ($this->_userModel) {
			return $this->_userModel;
		}
		return $this->_userModel = ClassRegistry::init($this->_settings['model']);
	}

	/**
	 * Try to return the active (logged in) user
	 *
	 * @throws CakeException
	 * @return array
	 */
	protected function _getActiveUser() {
		$this->_useSession()
			|| $this->_useCookie()
			|| $this->_useGuestAccount();
		return $this->Session->read($this->_settings['sessionKey']);
	}

	/**
	 * If Session is set, user is already logged in.
	 *
	 * @return bool
	 */
	protected function _useSession() {
		return $this->Session->check($this->_settings['sessionKey']);
	}

	/**
	 * Try to login user via Cookie
	 *
	 * @return bool
	 */
	protected function _useCookie() {
		$token = $this->Cookie->read($this->_settings['cookieKey']);
		if (!$token) {
			return false;
		}
		$user = $this->login('cookie', $token);
		// delete the cookie once it is used
		$this->Cookie->delete($this->_settings['cookieKey']);
		if (!$user) {
			return false;
		}
		$this->persist($this->_settings['rememberFor']);
		return true;
	}

	/**
	 * Last resort: login the user as a guest.
	 *
	 * @return mixed
	 */
	protected function _useGuestAccount() {
		return $this->login('guest');
	}

	/**
	 * Extracts the model name of a plugin notated string Plugin.Model
	 * if the notation is actually used.
	 *
	 * @return string
	 */
	protected function _getModelName() {
		if (strpos($this->_settings['model'], '.') === false) {
			return $this->_settings['model'];
		}
		list($plugin, $model) = preg_split('/\./', $this->_settings['model']);
		return $model;
	}

}

class Authenticator {

	/**
	 * @var AuthenticatorComponent
	 */
	protected static $_instance;

	public function __construct(&$instance) {
		if (!self::$_instance) {
			self::$_instance = $instance;
		}
	}

	public static function get($field = null) {
		return self::$_instance->get($field);
	}

}
