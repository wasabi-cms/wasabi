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
 * @subpackage    Wasabi.Plugin.Core.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('BackendAppController', 'Core.Controller');

/**
 * @property GroupPermission $GroupPermission
 */
class PermissionsController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.GroupPermission'
	);

	/**
	 * Index action
	 * GET
	 *
	 * @return void
	 */
	public function index() {
		$permissions = $this->GroupPermission->find('all', array(
			'contain' => array(
				'Group'
			),
			'order' => 'GroupPermission.path ASC'
		));

		$plugins = array();
		foreach ($permissions as $p) {
			$plugin = $p['GroupPermission']['plugin'];
			$controller = $p['GroupPermission']['controller'];
			$action = $p['GroupPermission']['action'];
			$groupId = $p['Group']['id'];
			$plugins[$plugin][$controller][$action][$groupId]['permission_id'] = $p['GroupPermission']['id'];
			$plugins[$plugin][$controller][$action][$groupId]['name'] = $p['Group']['name'];
			$plugins[$plugin][$controller][$action][$groupId]['allowed'] = $p['GroupPermission']['allowed'];
		}

		$this->set(compact('plugins'));
	}

	/**
	 * Sync action
	 * GET
	 *
	 * @return void
	 */
	public function sync() {
		$actionMap = $this->Guardian->getActionMap();

		// delete guest actions
		$this->GroupPermission->deleteAll(array(
			'path' => $this->Guardian->getGuestActions()
		));

		// check existance of all permission entries for each individual group
		$groups = $this->GroupPermission->Group->findAll(array(
			'conditions' => array(
				'Group.id <>' => 1 // ignore Administrator group
			)
		));

		foreach ($groups as $group) {
			// create missing permissions
			$groupPermissions = Set::extract('/GroupPermission/path', $this->GroupPermission->find('all', array(
				'conditions' => array(
					'GroupPermission.group_id' => $group['Group']['id']
				)
			)));
			$missingGroupPermissions = array_diff(array_keys($actionMap), $groupPermissions);
			foreach ($missingGroupPermissions as $missingPath) {
				$action = $actionMap[$missingPath];
				$this->GroupPermission->create();
				$this->GroupPermission->save(array(
					'group_id' => $group['Group']['id'],
					'path' => $missingPath,
					'plugin' => $action['plugin'],
					'controller' => $action['controller'],
					'action' => $action['action']
				));
			}
			// delete orphans
			$orphans = array_diff($groupPermissions, array_keys($actionMap));
			if (!empty($orphans)) {
				$this->GroupPermission->deleteAll(array(
					'GroupPermission.group_id' => $group['Group']['id'],
					'GroupPermission.path' => $orphans
				));
			}
		}

		Cache::clear(false, 'core.group_permissions');

		$this->Session->setFlash(__d('core', 'All permissions have been synchronized.'), 'default', array('class' => 'success'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Update action
	 * POST|AJAX POST
	 */
	public function update() {
		if ($this->request->is('post') && !empty($this->request->data)) {
			if ($this->GroupPermission->saveAll($this->request->data['GroupPermission'])) {
				Cache::clear(false, 'core.group_permissions');
				if ($this->request->is('ajax')) {
					$status = 'success';
					$this->set(compact('status'));
					$this->set('_serialize', array('status'));
				}
			}
		}
	}

}
