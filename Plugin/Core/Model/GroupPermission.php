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

App::uses('Cache', 'Cache');
App::uses('CoreAppModel', 'Core.Model');

/**
 * @property Group $Group
 */
class GroupPermission extends CoreAppModel {

	public $belongsTo = array(
		'Group' => array(
			'className' => 'Core.Group'
		)
	);

	public function findAllForGroup($groupId) {
		if (!$groupId) {
			return array();
		}
		if (!$permissions = Cache::read($groupId, 'core.group_permissions')) {
			$permissions = array();
			$groupPermissions = $this->find('all', array(
				'conditions' => array(
					$this->alias . '.group_id' => $groupId,
					$this->alias . '.allowed' => true
				)
			));
			foreach ($groupPermissions as $groupPermission) {
				$permissions[] = $groupPermission['GroupPermission']['path'];
			}
			Cache::write($groupId, $permissions, 'core.group_permissions');
		}
		return $permissions;
	}

}
