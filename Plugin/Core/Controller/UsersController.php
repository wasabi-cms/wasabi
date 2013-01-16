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

}
