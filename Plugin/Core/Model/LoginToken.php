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

class LoginToken extends CoreAppModel {

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'Core.User'
		)
	);

	/**
	 * Find an active token.
	 *
	 * @param string $token
	 * @param array $options
	 * @return array
	 */
	public function findActiveToken($token, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.token'      => $token,
			$this->alias . '.expires >=' => date('Y-m-d H:i:s')
		);
		return $this->find('first', Hash::merge($options, $opts));
	}

	/**
	 * Add a new token.
	 *
	 * @param integer $userId
	 * @param string $token
	 * @param string $duration
	 * @return mixed
	 */
	public function add($userId, $token, $duration) {
		$this->create();
		return $this->save(array(
			$this->alias => array(
				'user_id' => $userId,
				'token'   => $token,
				'expires' => date('Y-m-d H:i:s', strtotime($duration))
			)
		));
	}

	/**
	 * Check if a token already exists.
	 *
	 * @param string $token
	 * @return bool
	 */
	public function alreadyExists($token) {
		$found = $this->find('first', array(
			'conditions' => array(
				$this->alias . '.token' => $token
			)
		));
		return !empty($found);
	}

	/**
	 * Delete all expired tokens.
	 *
	 * @return boolean
	 */
	public function deleteExpiredTokens() {
		return $this->deleteAll(array(
			$this->alias . '.expires <' => date('Y-m-d H:i:s')
		));
	}

}
