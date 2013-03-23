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

App::uses('FrontendAppController', 'Core.Controller');

class FrontendErrorController extends FrontendAppController {

	public function beforeFilter() {
		parent::beforeFilter();

		// important: the layout has to be changed AFTER
		// the parent beforeFilter has run
		$this->layout = 'error';
		$this->viewPath = 'Errors';
	}

}
