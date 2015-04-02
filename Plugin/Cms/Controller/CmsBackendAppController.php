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
 * @subpackage    Wasabi.Plugin.Cms.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('BackendAppController', 'Core.Controller');

class CmsBackendAppController extends BackendAppController {

	/**
	 * The default View Class for all backend controller actions of the Cms plugin.
	 * Main purpose is to reflect properties of custom helpers into the view
	 * by simple phpdoc annotations ontop of the CmsView class.
	 *
	 * @var string
	 */
	public $viewClass = 'Cms.Cms';

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->layout = 'Cms.default';
	}
}
