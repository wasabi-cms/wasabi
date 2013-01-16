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
 * @property AuthenticatorComponent  $Authenticator
 * @property RequestHandlerComponent $RequestHandler
 */

class BackendAppController extends AppController {

	/**
	 * Components used by this controller
	 *
	 * @var array
	 */
	public $components = array(
		'RequestHandler',
		'Core.Authenticator' => array(
			'model' => 'Core.User',
			'sessionKey' => 'wasabi',
			'cookieKey' => 'wasabi_remember'
		)
	);

	/**
	 * Helpers used by this controller
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form',
		'Html',
		'Session'
	);

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		$this->_checkPermissions();
		$this->_setupBackend();
	}

	/**
	 * Redirect wrapper for testing
	 *
	 * @param array|string $url
	 * @param integer|null $status
	 * @param bool $exit
	 * @return bool|void
	 */
	public function redirect($url, $status = null, $exit = true) {
		parent::redirect($url, $status, $exit);
		return false;
	}

	/**
	 * Check if the current request needs an authenticated user.
	 * Check if the user is authorized to complete the request.
	 *
	 * @return void
	 */
	protected function _checkPermissions() {
		$user = $this->Authenticator->get();
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
		$this->layout = 'Core.default';
		$this->set('backend_prefix', Configure::read('Wasabi.backend_prefix'));
	}

	/**
	 * Load all backend menu items of all active plugins
	 * via plugin event handlers.
	 *
	 * @return void
	 */
	protected function _loadBackendMenu() {
		$event_name = 'Backend.Menu.load';
		$menu_items = WasabiEventManager::trigger($this, $event_name);
		if (empty($menu_items)) {
			return;
		}
		$menu_items = $menu_items[$event_name];

		$primary_menu = array();
		$secondary_menu = array();

		foreach ($menu_items as $items) {
			$secondary_active_found = false;
			$secondary_items = $items['primary']['children'];
			foreach ($secondary_items as &$secondary_item) {
				if ($secondary_item['url']['plugin'] === $this->request->params['plugin']
					&& $secondary_item['url']['controller'] === $this->request->params['controller']) {
					$secondary_active_found = true;
					$secondary_item['active'] = true;
					$secondary_menu = $secondary_items;
					break;
				}
			}
			if ($secondary_active_found) {
				$items['primary']['active'] = true;
			}
			unset($items['primary']['children']);
			$primary_menu[] = $items['primary'];
		}

		$this->set('backend_menu_for_layout', array(
			'primary' => $primary_menu,
			'secondary' => $secondary_menu
		));
	}

}
