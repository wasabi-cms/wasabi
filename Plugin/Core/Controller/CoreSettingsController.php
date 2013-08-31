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
 * @property CoreSetting $CoreSetting
 * @property array $data
 */

class CoreSettingsController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.CoreSetting'
	);

	/**
	 * Edit action
	 * GET | POST
	 *
	 * @return void
	 */
	public function edit() {
		$this->set('title_for_layout', __d('core', 'Edit Core Settings'));
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->CoreSetting->find('keyValues');
		} else {
			if ($this->CoreSetting->saveKeyValues($this->data)) {
				$this->Session->setFlash(__d('core', 'The core settings have been updated.'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'edit'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$this->set('cacheDurations', $this->CoreSetting->cacheDurations);
	}

}
