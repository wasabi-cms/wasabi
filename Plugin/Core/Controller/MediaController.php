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
 * @property Media $Media
 * @property array $data
 */

class MediaController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Media'
	);

	/**
	 * Index action
	 * GET
	 */
	public function index() {

	}

	/**
	 * Upload action
	 * AJAX POST | AJAX PUT
	 *
	 * @throws CakeException
	 */
	public function upload() {
		if (!$this->request->is('ajax') || !($this->request->is('post') || $this->request->is('put'))) {
			throw new CakeException($this->invalidRequestMessage, 400);
		}
	}

}
