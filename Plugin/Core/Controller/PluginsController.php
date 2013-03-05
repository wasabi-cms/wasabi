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
App::uses('Folder', 'Utility');

/**
 * @property Plugin $Plugin
 * @property array $data
 */

class PluginsController extends BackendAppController {

	/**
	 * Models used by this controller
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Plugin'
	);

	/**
	 * Index action
	 * GET
	 *
	 * @return void
	 */
	public function index() {
		$plugins = $this->Plugin->findAll();
		$this->set(array(
			'plugins' => $plugins,
			'title_for_layout' => __d('core', 'All Plugins')
		));
	}

	/**
	 * Activate a plugin by name $plugin
	 * POST
	 *
	 * @param string $plugin
	 * @return void
	 */
	public function activate($plugin = null) {
		if ($plugin === null || !$this->request->is('post') || !$this->Plugin->exists($plugin) || !$this->Plugin->isInstalled($plugin)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->isActive($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already active.', array($plugin)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->activate($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been activated.', array($plugin)), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been activated.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Deactivate a plugin by name $plugin
	 * POST
	 *
	 * @param string $plugin
	 */
	public function deactivate($plugin = null) {
		if ($plugin === null || !$this->request->is('post') || !$this->Plugin->exists($plugin) || !$this->Plugin->isInstalled($plugin)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!$this->Plugin->isActive($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already inactive.', array($plugin)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->deactivate($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been deactivated.', array($plugin)), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been deactivated.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Install a plugin with name $plugin
	 * POST
	 *
	 * @param string $plugin
	 * @return void
	 */
	public function install($plugin = null) {
		if ($plugin === null || !$this->request->is('post') || !$this->Plugin->exists($plugin)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->isInstalled($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already installed.', array($plugin)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->install($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been installed.', array($plugin)), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been installed.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Uninstall a plugin with name $name
	 * POST
	 *
	 * @param string $plugin
	 * @return void
	 */
	public function uninstall($plugin = null) {
		if ($plugin === null || !$this->request->is('post') || !$this->Plugin->exists($plugin) || $this->Plugin->isActive($plugin)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!$this->Plugin->isInstalled($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is not installed.', array($plugin)), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Plugin->uninstall($plugin)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been uninstalled and can now be removed from the file system.', array($plugin)), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been installed.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

}
