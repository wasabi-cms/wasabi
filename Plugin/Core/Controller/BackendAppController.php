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
App::uses('Cache', 'Cache');
App::uses('ClassRegistry', 'Utility');
App::uses('Configure', 'Core');
App::uses('Inflector', 'Utility');
App::uses('WasabiNav', 'Core.Lib');

/**
 * @property AuthenticatorComponent  $Authenticator
 * @property GuardianComponent       $Guardian
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
		'Core.Guardian',
		'RequestHandler',
		'Core.Menus',
		'Core.Filter'
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
	 * Holds all public accessable action paths.
	 *
	 * @var array
	 */
	public $guestActions;

	/**
	 * Helpers used by this controller
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form',
		'Html' => array(
			'className' => 'Core.CoreHtml'
		),
		'Session',
		'WasabiAsset' => array(
			'className' => 'Core.WasabiAsset'
		),
		'Navigation' => array(
			'className' => 'Core.Navigation'
		),
		'CForm' => array(
			'className' => 'Core.CForm'
		),
		'Image' => array(
			'className' => 'Core.Image'
		),
		'Filter' => array(
			'className' => 'Core.Filter'
		),
		'Bulk' => array(
			'className' => 'Core.Bulk'
		)
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

		$this->_checkBelowIE8();
		$this->_checkPermissions();
		$this->_setupBackend();
	}

	public function beforeRender() {
		parent::beforeRender();

		if (!isset($this->viewVars['bodyCssClass'])) {
			$this->set('bodyCssClass', array());
		}
		$this->set('bodyCssClass', array_merge((array) $this->viewVars['bodyCssClass'], array(
			Inflector::underscore($this->plugin) . '-' . Inflector::underscore($this->name) . '-' . Inflector::underscore($this->request->params['action'])
		)));
	}

	protected function _checkBelowIE8() {
		if (!isset($_SERVER['HTTP_USER_AGENT'])) {
			return;
		}

		$notSupported = array(
			'plugin' => 'core',
			'controller' => 'browser',
			'action' => 'notSupported'
		);

		if ($this->request->params['plugin'] === $notSupported['plugin'] &&
			$this->request->params['controller'] === $notSupported['controller'] &&
			$this->request->params['action'] === $notSupported['action']
		) {
			return;
		}

		preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
		if (count($matches) > 1 && (int) $matches[1] <= 7) {
			$this->redirect($notSupported);
		}
	}

	/**
	 * Check if the current request needs an authenticated user.
	 * Check if the user is authorized to complete the request.
	 *
	 * @return void
	 * @throws UnauthorizedException
	 */
	protected function _checkPermissions() {
		$url = array(
			'plugin' => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action' => $this->request->params['action']
		);

		if (!$this->Guardian->hasAccess($url)) {

			// user is not logged in
			if (!$this->Authenticator->get()) {
				// ajax request
				if ($this->request->is('ajax')) {
					throw new UnauthorizedException(__d('core', 'Your Session has expired. Please login again.'), 401);
				// normal request -> save current request and redirect to login page
				} else {
					$this->Session->write('login_referer', '/' . $this->request->url);
					$this->redirect(array(
						'plugin' => 'core',
						'controller' => 'users',
						'action' => 'login'
					));
					return;
				}
			}

			// user is logged in, but unauthorized to complete the request
			throw new UnauthorizedException(__d('core', 'You are not authorized to access this location'));
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
		$this->loadLanguages(null, true);

		if (!$this->_isLoginAction() && !$this->_isLogoutAction()) {
			$this->_loadBackendMenu();
		}
		$this->layout = 'Core.default';
		$this->formErrorMessage = __d('core', 'Please correct the marked errors.');
		$this->invalidRequestMessage = __d('core', 'Invalid Request.');
	}

	protected function _isLoginAction() {
		return $this->request->params['plugin'] === 'core' &&
			$this->request->params['controller'] === 'users' &&
			$this->request->params['action'] === 'login';
	}

	protected function _isLogoutAction() {
		return $this->request->params['plugin'] === 'core' &&
		$this->request->params['controller'] === 'users' &&
		$this->request->params['action'] === 'logout';
	}

	/**
	 * Load all backend menu items of all active plugins
	 * via plugin event handlers.
	 *
	 * @return void
	 */
	protected function _loadBackendMenu() {
		$this->_triggerEvent(new stdClass(), 'Backend.Menu.load');

		try {
			$mainItems = WasabiNav::getProcessedMenu('main', $this->request->params);
		} catch (CakeException $e) {
			return;
		}

		if (empty($mainItems)) {
			return;
		}

		$this->set(array(
			'backend_menu_for_layout' => array(
				'main' => $mainItems
			)
		));
	}

	/**
	 * Load and setup all languages and language related config options.
	 *
	 * @param integer $langId The id of the current active language.
	 * @param boolean $backend
	 * @return void
	 */
	public static function loadLanguages($langId = null, $backend = false) {
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

		if ($backend === true) {
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
			if (CakeSession::check('Wasabi.content_language_id')) {
				$cLangId = CakeSession::read('Wasabi.content_language_id');
				foreach ($languages['frontend'] as $cLang) {
					if ($cLang['id'] == $cLangId) {
						$contentLanguage = $cLang;
						break;
					}
				}
			}
			Configure::write('Wasabi.content_language', $contentLanguage);
		} else {
			// Frontend
			if ($langId !== null) {
				foreach ($languages['frontend'] as $frontendLanguage) {
					if ($frontendLanguage['id'] == $langId) {
						Configure::write('Wasabi.content_language', $frontendLanguage);
						Configure::write('Config.language', $frontendLanguage['iso']);
						break;
					}
				}
			} else {
				Configure::write('Wasabi.content_language', $languages['frontend'][0]);
				Configure::write('Config.language', $languages['frontend'][0]['iso']);
			}
		}
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
