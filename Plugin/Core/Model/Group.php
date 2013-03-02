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

/**
 * @property User $User
 */

class Group extends CoreAppModel {

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'User' => array(
			'className' => 'Core.User'
		)
	);

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a name for the group.'
			)
		)
	);

	/**
	 * Find all groups with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find a single group by id
	 *
	 * @param $id
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
	 * Move all users from a specific group to another group.
	 *
	 * @param integer $from_id
	 * @param integer $to_id
	 * @return bool
	 */
	public function moveUsersToNewGroup($from_id, $to_id) {
		if ($this->exists($from_id) && $this->exists($to_id)) {
			$user_ids = $this->User->find('list', array(
				'fields' => 'User.id',
				'contain' => array('Group'),
				'conditions' => array(
					'Group.id' => $from_id
				)
			));
			$data = array();
			foreach ($user_ids as $key => $user_id) {
				$data[] = array(
					'User' => array(
						'id' => $user_id,
						'group_id' => $to_id
					)
				);
			}
			return $this->User->saveAll($data);
		}

		return false;
	}

}
