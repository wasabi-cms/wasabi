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
 * @subpackage    Wasabi.Plugin.Core.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Folder', 'Utility');
App::uses('CakePlugin', 'Core');
App::uses('ClassRegistry', 'Utility');

class MenusComponent extends Component {

	/**
	 * Holds all menus for layout.
	 *
	 * @var array
	 */
	public $menus = array();

	/**
	 * Holds the Menu model
	 *
	 * @var Menu
	 */
	public $Menu;

	/**
	 * Called before the Controller::beforeFilter().
	 *
	 * @param Controller $controller Controller with components to initialize
	 * @return void
	 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);
		if (isset($controller->Menu)) {
			$this->Menu = $controller->Menu;
		} else {
			$this->Menu = ClassRegistry::init('Core.Menu');
		}
	}

	/**
	 * Called after the Controller::beforeFilter() and before the controller action.
	 *
	 * @param Controller $controller
	 * @return void
	 */
	public function startup(Controller $controller) {
		if ($controller instanceof BackendAppController) {
//			var_dump('backend');
		} else {
//			var_dump('frontend');
		}
	}

	/**
	 * Called after Controller::beforeRender()
	 *
	 * @param Controller $controller
	 */
	public function beforeRender(Controller $controller) {
		$controller->set('menus_for_layout', $this->menus);
	}

}
