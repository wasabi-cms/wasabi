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
 * @property Group $Group
 * @property array $data
 */

class GroupsController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Group'
	);

	/**
	 * Index action
	 * GET
	 *
	 * @return void
	 */
	public function index() {
		$groups = $this->Group->findAll();
		$this->set(array(
			'groups' => $groups,
			'title_for_layout' => __d('core', 'All Groups')
		));
	}

	/**
	 * Add action
	 * GET | POST
	 *
	 * @return void
	 */
	public function add() {
		$this->set('title_for_layout', __d('core', 'Add a new Group'));
		if ($this->request->is('post') && !empty($this->data)) {
			if ($this->Group->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The group <strong>%s</strong> has been added.', array($this->data['Group']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index')); return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Edit action
	 * GET | POST
	 *
	 * @param null|integer $id
	 * @return void
	 */
	public function edit($id = null) {
		if ($id === null || !$this->Group->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index')); return;
		}
		$this->set('title_for_layout', __d('core', 'Edit Group'));
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->Group->findById($id);
		} else {
			if ($this->Group->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The group <strong>%s</strong> has been updated successfully.', array($this->data['Group']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index')); return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
		$this->render('add');
	}

	/**
	 * Delete action
	 * POST
	 *
	 * @param null|integer $id
	 * @return void
	 * @throws MethodNotAllowedException
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		if ($id === null || $id == 1 || !$this->Group->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index')); return;
		}

		$group = $this->Group->findById($id);

		$group_can_be_deleted = false;

		// check user count of group
		$user_count = (int) $group['Group']['user_count'];
		if ($user_count == 0) {
			$group_can_be_deleted = true;
		}

		// check if alternative $data['Group']['alternative_group_id'] is set
		// to move the existing users of this group to a new group before deletion
		if (isset($this->data['Group']['alternative_group_id'])) {
			// move users to this group
			if ($this->Group->moveUsersToNewGroup($id, $this->data['Group']['alternative_group_id'])) {
				$group_can_be_deleted = true;
			}
		}

		if ($group_can_be_deleted === true) {
			if ($this->Group->delete($id)) {
				if ($user_count > 0) {
					$this->Session->setFlash(__d('core', 'The group has been deleted. Prior %s group members have been moved to the alternative group.', array($user_count)), 'default', array('class' => 'success'));
				} else {
					$this->Session->setFlash(__d('core', 'The group has been deleted.'), 'default', array('class' => 'success'));
				}
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->set(array(
				'group' => $group,
				'groups' => $this->Group->find('list', array(
					'fields' => array('Group.id', 'Group.name'),
					'conditions' => array(
						'Group.id <>' => $id
					)
				))
			));
		}
	}

}
