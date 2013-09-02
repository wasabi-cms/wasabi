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
 * @property CoreCacheSetting $CoreCacheSetting
 * @property CoreGeneralSetting $CoreGeneralSetting
 * @property CoreMediaSetting $CoreMediaSetting
 * @property array $data
 */

class CoreSettingsController extends BackendAppController {

	/**
	 * General action
	 * GET | POST
	 *
	 * Edit General Settings.
	 *
	 * @return void
	 */
	public function general() {
		$this->set('title_for_layout', __d('core', 'Edit General Settings'));
		$this->CoreGeneralSetting = ClassRegistry::init('Core.CoreGeneralSetting');
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->CoreGeneralSetting->find('keyValues');
		} else {
			if ($this->CoreGeneralSetting->saveKeyValues($this->data)) {
				$this->Session->setFlash(__d('core', 'The general settings have been updated.'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'general'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
	}

	/**
	 * Cache action
	 * GET | POST
	 *
	 * Edit Cache settings.
	 *
	 * @return void
	 */
	public function cache() {
		$this->set('title_for_layout', __d('core', 'Edit Cache Settings'));
		$this->CoreCacheSetting = ClassRegistry::init('Core.CoreCacheSetting');
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->CoreCacheSetting->find('keyValues');
		} else {
			if ($this->CoreCacheSetting->saveKeyValues($this->data)) {
				$this->Session->setFlash(__d('core', 'The cache settings have been updated.'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'cache'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$this->set('cacheDurations', $this->CoreCacheSetting->cacheDurations);
	}

	/**
	 * Media action
	 * GET | POST
	 *
	 * Edit Media settings.
	 *
	 * @return void
	 */
	public function media() {
		$this->set('title_for_layout', __d('core', 'Edit Media Settings'));
		$this->CoreMediaSetting = ClassRegistry::init('Core.CoreMediaSetting');
		if (!$this->request->is('post') && empty($this->data)) {
			$this->request->data = $this->CoreMediaSetting->find('keyValues');
		} else {
			if ($this->CoreMediaSetting->saveKeyValues($this->data)) {
				$this->Session->setFlash(__d('core', 'The media settings have been updated.'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'cache'));
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}
	}

}
