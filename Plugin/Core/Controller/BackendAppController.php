<?php
/**
 * Backend wide base controller class
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

App::uses('AppController', 'Controller');

/**
 * @property RequestHandlerComponent $RequestHandler
 */

class BackendAppController extends AppController {

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		$this->RequestHandler = $this->Components->load('RequestHandler');
		$this->helpers = array_merge($this->helpers, array(
			'Session'
		));

		$this->_checkPermissions();
		$this->_setupBackend();
	}

	/**
	 * Check if the current request needs an authenticated user.
	 * Check if the user is authorized to complete the request.
	 *
	 * @return void
	 */
	protected function _checkPermissions() {

	}

	/**
	 * Setup backend
	 * - layout
	 * - interface language
	 * - content language
	 * - backend menu items
	 *
	 * @return void
	 */
	protected function _setupBackend() {
		$this->_loadBackendMenu();
	}

	/**
	 * Load all backend menu items of all active plugins
	 * via event callbacks
	 *
	 * @return void
	 */
	protected function _loadBackendMenu() {

	}

}