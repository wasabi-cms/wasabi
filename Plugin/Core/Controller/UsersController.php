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
 * @property User $User
 * @property array $data
 */

class UsersController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.User'
	);

	/**
	 * Index action
	 * GET
	 *
	 * @return void
	 */
	public function index() {
		$users = $this->User->findAll(array(
			'contain' => array(
				'Group'
			)
		));
		$this->set(array(
			'users' => $users,
			'title_for_layout' => __d('core', 'All Users')
		));
	}

	/**
	 * Add action
	 * GET | POST
	 *
	 * @return void
	 */
	public function add() {
		$this->_prepareAddEdit();
		if ($this->request->is('post') && !empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The User <strong>%s</strong> has been added.', array($this->data['User']['username'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
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
		if ($id === null || ($id == 1 && Authenticator::get('User.id') != 1)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		$this->_prepareAddEdit();
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->User->findById($id);
			unset($this->User->validate['password_unencrypted']);
			unset($this->User->validate['password_confirmation']);
		} else {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__d('core', 'The user <strong>%s</strong> has been updated successfully.', array($this->data['User']['username'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
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

		if ($id === null || !$this->User->canBeDeleted($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}

		if ($this->User->delete($id)) {
			$this->Session->setFlash(__d('core', 'The user has been deleted.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->setFlash(__d('core', 'The user has not been deleted.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Login action
	 * GET | POST
	 *
	 * @return void
	 */
	public function login() {
		$this->layout = 'Core.login';
		$this->set('title_for_layout', __d('core', 'Login'));

		if ($this->Session->check('login_referer')) {
			$this->set('login_referer', $this->Session->read('login_referer'));
			$this->Session->delete('login_referer');
		}

		if (!empty($this->data)) {
			if ($this->Authenticator->login('credentials', $this->data['User'])) {
				$this->Session->setFlash(__d('core', 'Welcome back, %s!', array($this->Authenticator->get('username'))), 'default', array('class' => 'success'));
			} else {
				$this->Session->setFlash(__d('core', 'Wrong username or password.'), 'default', array('class' => 'error'));
			}
		}

		$user = Authenticator::get();
		if (!empty($user)) {
			// default redirect: first primary backend menu item
			$redirect = $this->viewVars['backend_menu_for_layout']['primary'][0]['url'];
			// override default redirect if a login_referer is submitted
			if (!empty($this->data) && isset($this->data['User']['login_referer'])) {
				$redirect = '/' . $this->data['User']['login_referer'];
			}
			$this->redirect($redirect);
		}
	}

	/**
	 * Logout action
	 * GET
	 *
	 * @return void
	 */
	public function logout() {
		if ($this->Authenticator->logout()) {
			$this->Session->setFlash(__d('core', 'See you soon.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'login'));
		}
	}

	/**
	 * Init view variables that are required for add/edit action.
	 *
	 * @return void
	 */
	private function _prepareAddEdit() {
		$title = __d('core', 'Add a new User');
		if ($this->request->params['action'] == 'edit') {
			$title = __d('core', 'Edit User');
		}
		$this->set(array(
			'groups' => $this->User->Group->find('list', array('Group.id', 'Group.name')),
			'languages' => $this->User->Language->find('list', array('Language.id', 'Language.name')),
			'title_for_layout' => $title
		));
	}

}
