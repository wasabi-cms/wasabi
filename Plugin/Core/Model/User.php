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
App::uses('Security', 'Utility');

class User extends CoreAppModel {

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
		$user = $this->find('first', array_merge($options, $opts));
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
	 * @return array
	 */
	public function findById($id) {
		return $this->find('first', array(
			'conditions' => array(
				$this->alias . '.id' => $id
			)
		));
	}

}
