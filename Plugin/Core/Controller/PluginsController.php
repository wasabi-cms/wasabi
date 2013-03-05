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
	 * Find new plugins in Plugin directory.
	 * Remove plugins from DB if they are not in the filesystem and are inactive and not installed.
	 * Flag plugins that are in DB and are not in the filesystem but are installed.
	 * Unflag plugins that have been flagged but are now ready to uninstall.
	 * GET
	 *
	 * @return void
	 */
	public function update() {
		$plugins_folder = new Folder(APP . 'Plugin' . DS, false);
		$existing_plugins = $plugins_folder->read(true, array('.', 'Core', 'Migrations'));
		$existing_plugins = $existing_plugins[0];

		$plugins_in_db = $this->Plugin->find('list', array(
			'fields' => array('Plugin.name', 'Plugin.id')
		));

		$new_plugins = array();
		foreach ($existing_plugins as $pname) {
			if (!isset($plugins_in_db[$pname])) {
				$data = array(
					'Plugin' => array(
						'name' => $pname,
						'active' => 0,
						'installed' => 0
					)
				);
				$this->Plugin->create();
				if ($this->Plugin->save($data)) {
					$new_plugins[] = $pname;
				}
			}
		}

		$removed_plugins = array();
		$flagged_plugins = array();
		$unflagged_plugins = array();
		foreach ($plugins_in_db as $pname => $pid) {
			$plugin_at_filesystem = false;
			foreach ($existing_plugins as $epname) {
				if ($epname === $pname) {
					$plugin_at_filesystem = true;
					break;
				}
			}

			$plugin = $this->Plugin->findById($pid);
			// plugin is in db, but not in filesystem
			if (!$plugin_at_filesystem) {
				// if the plugin is inactive and not installed
				if ($plugin['Plugin']['active'] === false && $plugin['Plugin']['installed'] === false) {
					//delete the plugin from db
					$this->Plugin->delete($pid);
					$removed_plugins[] = $pname;
				// plugin was removed from file system without proper uninstallation
				} else {
					// flag the plugin
					$this->Plugin->flag($plugin['Plugin']['id']);
					$flagged_plugins[] = $pname;
				}
			// plugin exists at filesystem
			} else {
				// remove its flag
				if ($plugin['Plugin']['flagged'] === true) {
					$this->Plugin->unflag($plugin['Plugin']['id']);
					$unflagged_plugins[] = $pname;
				}
			}
		}

		$flash_messages = array();
		if (empty($new_plugins) && empty($removed_plugins) && empty($flagged_plugins) && empty($unflagged_plugins)) {
			$flash_messages[] = __d('core', 'Nothing to change.');
		}
		if (!empty($new_plugins)) {
			$flash_messages[] = __d('core', '<strong>%s</strong> plugins have been added.', array(count($new_plugins)));
		}
		if (!empty($removed_plugins)) {
			$flash_messages[] = __d('core', '<strong>%s</strong> plugins have been removed.', array(count($removed_plugins)));
		}
		if (!empty($flagged_plugins)) {
			$flash_messages[] = __d('core', '<strong>%s</strong> plugins have been flagged.', array(count($flagged_plugins)));
		}
		if (!empty($unflagged_plugins)) {
			$flash_messages[] = __d('core', '<strong>%s</strong> plugins have been unflagged.', array(count($unflagged_plugins)));
		}
		$this->Session->setFlash(join(' ', $flash_messages), 'default', array('class' => 'success'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Activate a plugin by $id
	 * POST
	 *
	 * @param integer $id
	 * @return void
	 */
	public function activate($id = null) {
		if ($id === null || !$this->request->is('post') || !$this->Plugin->exists($id) || !$this->Plugin->isInstalled($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->isActive($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already activated.', array($plugin['Plugin']['name'])), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->activate($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been activated.', array($plugin['Plugin']['name'])), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been activated.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Deactivate a plugin by $id
	 * POST
	 *
	 * @param null $id
	 */
	public function deactivate($id = null) {
		if ($id === null || !$this->request->is('post') || !$this->Plugin->exists($id) || !$this->Plugin->isInstalled($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->isNotActive($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already inactive.', array($plugin['Plugin']['name'])), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->deactivate($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been deactivated.', array($plugin['Plugin']['name'])), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been deactivated.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Install a plugin with id $id
	 * POST
	 *
	 * @param integer $id
	 * @return void
	 */
	public function install($id = null) {
		if ($id === null || !$this->request->is('post') || !$this->Plugin->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->isInstalled($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is already installed.', array($plugin['Plugin']['name'])), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->install($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been installed.', array($plugin['Plugin']['name'])), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been installed.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

	/**
	 * Uninstall a plugin with id $id
	 * POST
	 *
	 * @param integer $id
	 * @return void
	 */
	public function uninstall($id = null) {
		if ($id === null || !$this->request->is('post') || !$this->Plugin->exists($id) || $this->Plugin->isActive($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->isNotInstalled($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> is not installed.', array($plugin['Plugin']['name'])), 'default', array('class' => 'info'));
			$this->redirect(array('action' => 'index'));
		}
		if ($plugin = $this->Plugin->uninstall($id)) {
			$this->Session->setFlash(__d('core', 'The plugin <strong>%s</strong> has been uninstalled and can now be removed from the file system.', array($plugin['Plugin']['name'])), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__d('core', 'The plugin has not been installed.'), 'default', array('class' => 'error'));
		$this->redirect(array('action' => 'index'));
	}

}
