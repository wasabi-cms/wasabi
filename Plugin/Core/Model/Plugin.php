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
 * @subpackage    Wasabi.Plugin.Core.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Plugin extends CoreAppModel {

	/**
	 * Default order
	 *
	 * @var string
	 */
	public $order = 'Plugin.name ASC';

	public function afterSave($created) {
		Cache::delete('active_plugins', 'core.plugins');
	}

	/**
	 * Find all plugins with find $options
	 *
	 * @param array $options
	 * @return array
	 */
	public function findAll($options = array()) {
		return $this->find('all', $options);
	}

	/**
	 * Find a single group by id
	 *
	 * @param $id
	 * @param array $options
	 * @return array|boolean
	 */
	public function findById($id, $options = array()) {
		$opts['conditions'] = array(
			$this->alias . '.id' => (int) $id
		);
		return $this->find('first', Hash::merge($options, $opts));
	}

	/**
	 * Find all active plugins.
	 *
	 * @return array
	 */
	public function findActive() {
		return $this->find('all', array(
			'conditions' => array(
				$this->alias . '.active' => 1
			)
		));
	}

	public function findAllInstalled() {
		return $this->find('all', array(
			'conditions' => array(
				$this->alias . '.installed' => true
			),
			'order' => $this->alias . '.name ASC'
		));
	}

	/**
	 * Flag a plugin to indicate it is not correctly uninstalled
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function flag($id) {
		return $this->save(array(
			'Plugin' => array(
				'id' => $id,
				'flagged' => 1
			)
		));
	}

	/**
	 * Unflag a plugin
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function unflag($id) {
		return $this->save(array(
			'Plugin' => array(
				'id' => $id,
				'flagged' => 0
			)
		));
	}

	/**
	 * Check if a plugin is active and return it.
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function isActive($id) {
		return $this->findById($id, array(
			'conditions' => array(
				'Plugin.active' => 1
			)
		));
	}

	/**
	 * Check if a plugin is not activated and return it.
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function isNotActive($id) {
		return $this->findById($id, array(
			'conditions' => array(
				'Plugin.active' => 0
			)
		));
	}

	/**
	 * Check if a plugin is installed and return it.
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function isInstalled($id) {
		return $this->findById($id, array(
			'conditions' => array(
				'Plugin.installed' => 1
			)
		));
	}

	/**
	 * Check if a plugin is not installed and return it.
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function isNotInstalled($id) {
		return $this->findById($id, array(
			'conditions' => array(
				'Plugin.installed' => 0
			)
		));
	}

	/**
	 * Activate a plugin by $id
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function activate($id) {
		$data = array(
			'Plugin' => array(
				'id' => $id,
				'active' => 1
			)
		);
		if ($this->save($data)) {
			return $this->findById($id);
		}
		return false;
	}

	/**
	 * Deactivate a plugin by $id
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function deactivate($id) {
		$data = array(
			'Plugin' => array(
				'id' => $id,
				'active' => 0
			)
		);
		if ($this->save($data)) {
			return $this->findById($id);
		}
		return false;
	}

	/**
	 * Install a plugin by $id
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function install($id) {
		$data = array(
			'Plugin' => array(
				'id' => $id,
				'installed' => 1
			)
		);
		if ($this->save($data)) {
			return $this->findById($id);
		}
		return false;
	}

	/**
	 * Uninstall a plugin by $id
	 *
	 * @param integer $id
	 * @return array|boolean
	 */
	public function uninstall($id) {
		$data = array(
			'Plugin' => array(
				'id' => $id,
				'installed' => 0
			)
		);
		if ($this->save($data)) {
			return $this->findById($id);
		}
		return false;
	}

}
