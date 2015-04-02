<?php
/**
 * Frontend wide base controller class
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

class FrontendAppController extends AppController {

	/**
	 * Components used by this controller
	 *
	 * @var array
	 */
	public $components = array(
		'RequestHandler',
		'Core.Menus'
	);

	/**
	 * Helpers used by this controller
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form',
		'Html',
		'Session',
		'WasabiAsset' => array(
			'className' => 'Core.WasabiAsset'
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
