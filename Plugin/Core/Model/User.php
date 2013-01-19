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
 * @subpackage    Wasabi.Plugin.Core.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CoreAppModel', 'Core.Model');
App::uses('Hash', 'Utility');
App::uses('Security', 'Utility');

/**
 * @property Group $Group
 * @property LoginToken $LoginToken
 */

class User extends CoreAppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Core.Group',
			'counterCache' => true
		),
		'Language' => array(
			'className' => 'Core.Language'
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'LoginToken' => array(
			'className' => 'Core.LoginToken',
			'dependent' => true
		)
	);

	/**
	 * Default order
	 *
	 * @var array
	 */
	public $order = array(
		'User.id ASC'
	);

	/**
	 * Find all users with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find an active user via username and password
	 *
	 * @param string $username
	 * @param string $password unencrypted password
	 * @param array $options
	 *
	 * @return array|boolean
	 */
	public function findActiveByCredentials($username, $password, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.username' => $username,
			$this->alias . '.active' => true
		);
		$user = $this->find('first', Hash::merge($options, $opts));
		if (!$user) {
			return false;
		}
		$bcrypt_password = Security::hash($password, 'blowfish', $user[$this->alias]['password']);
		return ($bcrypt_password == $user[$this->alias]['password']) ? $user : false;
	}

	/**
	 * Find a user by id
	 *
	 * @param integer $id
	 * @param array $options
	 * @return array
	 */
	public function findById($id, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.id' => (int) $id
		);
		return $this->find('first', Hash::merge($options, $opts));
	}

	/**
	 * Authenticate a user via 'guest', 'credentials' or 'cookie'
	 *
	 * 'credentials' needs username, password
	 * 'cookie'      needs token, duration
	 *
	 * @param string $type
	 * @param array|string $credentials
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function authenticate($type, $credentials = null) {
		switch ($type) {

			case 'guest':
				return array();

			case 'credentials':
				$username = $credentials['username'];
				$password = $credentials['password'];
				if ($username == '' || $password == '') {
					return array();
				}
				$user = $this->findActiveByCredentials($username, $password, array(
					'contain' => array(
						'Group',
						'Language'
					)
				));
				if (!$user) {
					return array();
				}
				return $user;

			case 'cookie':
				$token = $credentials;
				$user = $this->LoginToken->findActiveToken($token, array(
					'contain' => array(
						'User' => array(
							'conditions' => array(
								'User.active' => true
							),
							'Group',
							'Language'
						)
					)
				));
				if (!$user || (isset($user['User']) && $user['User']['id'] === null)) {
					return array();
				}
				$this->LoginToken->delete($user['LoginToken']['id']);
				unset($user['LoginToken']);
				return $user;

			default:
				return array();
		}
	}

	/**
	 * Persist a user login by generating a unique login token for that user
	 * that expires after the specified $duration.
	 *
	 * @param integer $user_id
	 * @param string $duration
	 * @return bool|string the generated token, or false if db error
	 */
	public function persist($user_id, $duration) {
		$token = $this->generateToken();
		while ($this->LoginToken->alreadyExists($token)) {
			$token = $this->generateToken();
		}
		if ($this->LoginToken->add($user_id, $token, $duration)) {
			return $token;
		}
		return false;
	}

	/**
	 * Generate a token
	 *
	 * @return string
	 */
	public function generateToken() {
		return md5(uniqid(mt_rand(), true));
	}

}
