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
		'Core.Authenticator' => array(
			'model' => 'Core.User'
		),
		'RequestHandler'
	);

	/**
	 * Default Session flash message when form errors are present.
	 *
	 * @var string
	 */
	public $formErrorMessage;

	/**
	 * Default Session flash message when a request is invalid.
	 *
	 * @var string
	 */
	public $invalidRequestMessage;

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
	 * The default View Class for all backend controller actions.
	 * Main purpose is to reflect properties of custom helpers into the view
	 * by simple phpdoc annotations ontop of the CoreView class.
	 *
	 * @var string
	 */
	public $viewClass = 'Core.Core';

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

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
		$plugin = $this->request->params['plugin'];
		$controller = $this->request->params['controller'];
		$action = $this->request->params['action'];
		$path = "${plugin}.${controller}.${action}";

		$guestActions = array(
			'core.users.login',
			'core.users.logout'
		);

		// action requires login
		if (!in_array($path, $guestActions)) {

			// user is not logged in -> save current request and redirect to login page
			if (!$this->Authenticator->get()) {
				$this->Session->write('login_referer', '/' . $this->request->url);
				$this->redirect(array(
					'plugin' => 'core',
					'controller' => 'users',
					'action' => 'login'
				));
			}
		}
	}

	/**
	 * Setup backend
	 * - layout
	 * - interface language
	 * - content language
	 * - backend menu items
	 * - backend_prefix
	 * - default form error message
	 * - default invalid request message
	 *
	 * @return void
	 */
	protected function _setupBackend() {
		$this->_loadBackendMenu();
		$this->_loadLanguages();
		$this->layout = 'Core.default';
		$this->formErrorMessage = __d('core', 'Please correct the marked errors.');
		$this->invalidRequestMessage = __d('core', 'Invalid Request.');
	}

	/**
	 * Load all backend menu items of all active plugins
	 * via plugin event handlers.
	 *
	 * @return void
	 */
	protected function _loadBackendMenu() {
		$eventName = 'Backend.Menu.load';
		$menuItems = $this->_triggerEvent($this, $eventName);
		if (empty($menuItems)) {
			return;
		}
		$menuItems = $menuItems[$eventName];

		$primaryMenu = array();
		$secondaryMenu = array();

		foreach ($menuItems as $items) {
			$secondaryActiveFound = false;
			$secondaryItems = $items['primary']['children'];
			foreach ($secondaryItems as &$secondaryItem) {
				if ($secondaryItem['url']['plugin'] === $this->request->params['plugin']
					&& $secondaryItem['url']['controller'] === $this->request->params['controller']) {
					$secondaryActiveFound = true;
					$secondaryItem['active'] = true;
					$secondaryMenu = $secondaryItems;
					break;
				}
			}
			if ($secondaryActiveFound) {
				$items['primary']['active'] = true;
			}
			unset($items['primary']['children']);
			$primaryMenu[] = $items['primary'];
		}

		$this->set('backend_menu_for_layout', array(
			'primary' => $primaryMenu,
			'secondary' => $secondaryMenu
		));
	}

	/**
	 * Load and setup all languages and language related config options.
	 *
	 * @return void
	 */
	protected function _loadLanguages() {
		// All available languages for frontend / backend
		if (!$languages = Cache::read('languages', 'core.infinite')) {
			$language = ClassRegistry::init('Core.Language');
			$allLanguages = $language->findAll();

			$languages = array(
				'frontend' => array(),
				'backend' => array()
			);
			foreach ($allLanguages as $lang) {
				if ($lang['Language']['available_at_backend'] === true) {
					$languages['backend'][] = $lang['Language'];
				}
				if ($lang['Language']['available_at_frontend'] === true) {
					$languages['frontend'][] = $lang['Language'];
				}
			}
			Cache::write('languages', $languages, 'core.infinite');
		}
		Configure::write('Languages', $languages);

		// current backend language of the logged in user
		$user = Authenticator::get();
		$userLanguage = $languages['backend'][0];
		if ($user && isset($user['Language']) && isset($user['Language']['id'])) {
			foreach ($languages['backend'] as $bLang) {
				if ($bLang['id'] == $user['Language']['id']) {
					$userLanguage = $bLang;
					break;
				}
			}
		}
		Configure::write('Wasabi.backend_language', $userLanguage);
		Configure::write('Config.language', $userLanguage['iso']);

		// current content language the user has active
		$contentLanguage = $languages['frontend'][0];
		if ($this->Session->check('Wasabi.content_language_id')) {
			$cLangId = $this->Session->read('Wasabi.content_language_id');
			foreach ($languages['frontend'] as $cLang) {
				if ($cLang['id'] == $cLangId) {
					$contentLanguage = $cLang;
					break;
				}
			}
		}
		Configure::write('Wasabi.content_language', $contentLanguage);
	}

	/**
	 * Wrapper to trigger an event via WasabiEventManager.
	 * The wrapper is needed to easily mock _triggerEvent for controller tests.
	 *
	 * @param object $origin
	 * @param string $eventName
	 * @param null|mixed $data
	 * @return array
	 */
	protected function _triggerEvent(&$origin, $eventName, $data = null) {
		return WasabiEventManager::trigger($origin, $eventName, $data);
	}

}
