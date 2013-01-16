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
			'className' => 'Core.Group'
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

}
