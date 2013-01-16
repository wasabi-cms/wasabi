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
	 *
	 * @return void
	 */
	public function index() {
		$users = $this->User->findAll(array(
			'contain' => array(
				'Group'
			)
		));
		$this->set('users', $users);
	}

	/**
	 * Login action
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
				$this->Session->setFlash(__d('core', 'Welcome back,  %s!', array($this->Authenticator->get('username'))), 'default', array('class' => 'success'));
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
	 *
	 * @return void
	 */
	public function logout() {
		if ($this->Authenticator->logout()) {
			$this->Session->setFlash(__d('core', 'See you soon.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'login'));
		}
	}

}
