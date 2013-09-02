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
	 * POST | PUT | AJAX POST | AJAX PUT
	 *
	 * Hint for iframe transport:
	 * --------------------------
	 * We don't respond with an error code of 400 on errors because even IE 10 will replace
	 * the iframe contents with an error message from disk (res://ieframe.dll/http_500.htm)
	 * which results in a cross-domain access denied error.
	 */
	public function upload() {
		$status = 'success';
		$data = array();
		$errors = array();
		$_serialize = array('status');

		// Check for valid request type
		if (!($this->request->is('post') || $this->request->is('put'))) {
			$errors[] = array('message' => __d('core', $this->invalidRequestMessage));
		}

		// Make sure at least one file is submitted.
		if (empty($errors) && (!isset($this->data['files']) || empty($this->data['files']))) {
			$errors[] = array('message' => __d('core', 'No files were submitted.'));
		}

		// Validate submitted files
		if (empty($errors)) {
			foreach ($this->data['files'] as $file) {
				$fileErrors = $this->Media->validateFile($file);
				if (empty($fileErrors)) {
					$data[] = $this->Media->data;
				} else {
					$errors = $errors + $fileErrors;
				}
			}
		}

		// Save submitted files and catch afterSave errors.
		if (empty($errors) && !$this->Media->saveAll($this->data['files'], array('validate' => false))) {
			$errors[] = array('message' => __d('core', 'Something went wrong.'));
		}

		if (!empty($errors)) {
			$status = 'error';
			$_serialize[] = 'errors';
			$this->set('errors', $errors);
		}

		$this->set('status', $status);

		if ($this->request->is('ajax')) {
			$this->set('_serialize', $_serialize);
		} else {
			$this->layout = false;
		}

	}

}
