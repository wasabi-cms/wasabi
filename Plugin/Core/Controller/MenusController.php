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
App::uses('MenuItem', 'Core.Model');

/**
 * @property Menu $Menu
 */
class MenusController extends BackendAppController {

	/**
	 * Models used by this controller.
	 *
	 * @var array
	 */
	public $uses = array(
		'Core.Menu'
	);

	/**
	 * Index action
	 * GET
	 */
	public function index() {
		$menus = $this->Menu->findAll();
		$this->set(array(
			'menus' => $menus,
			'title_for_layout' => __d('core', 'All Menus')
		));
	}

	/**
	 * Add action
	 * GET | POST
	 */
	public function add() {
		if ($this->request->is('post') && !empty($this->request->data)) {
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__d('core', 'The menu <strong>%s</strong> has been added successfully.', array($this->data['Menu']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$this->set(array(
			'title_for_layout' => __d('core', 'Add a new Menu')
		));
	}

	/**
	 * Edit action
	 * GET | POST
	 */
	public function edit($id = null) {
		if ($id === null || !$this->Menu->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		if (!$this->request->is('post') && empty($this->request->data)) {
			$this->request->data = $this->Menu->findById($id);
		} else {
			if ($this->Menu->save($this->request->data)) {
				$this->Session->setFlash(__d('core', 'The menu <strong>%s</strong> has been updated successfully.', array($this->data['Menu']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$this->set(array(
			'title_for_layout' => __d('core', 'Edit Menu'),
			'menuItems' => $this->Menu->MenuItem->find('threaded', array(
				'conditions' => array(
					'MenuItem.menu_id' => $id
				)
			))
		));
		$this->render('add');
	}

	/**
	 * Delete action
	 * POST
	 *
	 * @param null|integer $id
	 * @return void
	 * @throws MethodNotAllowedException
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		if ($id === null || !$this->Menu->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
			return;
		}

		if ($this->Menu->delete($id)) {
			$this->Session->setFlash(__d('core', 'The menu has been deleted.'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

	/**
	 * Add action
	 * GET | POST
	 */
	public function add_item($menuId = null, $parentId = null) {
		if ($menuId === null || !$this->Menu->exists($menuId)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('plugin' => 'core', 'controller' => 'menus', 'action' => 'index'));
			return;
		}
		if ($this->request->is('post') && !empty($this->request->data)) {
			if ($this->request->data['MenuItem']['parent_id'] === '') {
				$this->request->data['MenuItem']['parent_id'] = 0;
			}
			$this->Menu->MenuItem->Behaviors->load('Core.EnhancedTree', array(
				'scope' => array(
					'MenuItem.menu_id' => $this->request->data['MenuItem']['menu_id']
				)
			));
			if ($this->Menu->MenuItem->save($this->request->data)) {
				$this->Session->setFlash(__d('core', 'Menu Item <strong>%s</strong> has been updated.', array($this->request->data['MenuItem']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'edit', $this->request->data['MenuItem']['menu_id']));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		} else {
			$this->request->data['MenuItem']['menu_id'] = $menuId;
			if ($parentId !== null && $this->Menu->MenuItem->exists($parentId)) {
				$this->request->data['MenuItem']['parent_id'] = $parentId;
			}
		}

		$this->set(array(
			'menu' => $this->Menu->findById($menuId),
			'menus' => $this->Menu->find('list', array('order' => 'Menu.name ASC')),
			'parentItems' => $this->Menu->MenuItem->generateTreeList(array(
				'MenuItem.menu_id' => $menuId
			), null, null, '_', null, 2),
			'types' => $this->_getAvailableLinks()
		));
		$this->render('add_item');
	}

	/**
	 * Edit action
	 * GET | POST
	 */
	public function edit_item($id = null) {
		if ($id === null || !$this->Menu->MenuItem->exists($id)) {
			$this->Session->setFlash($this->invalidRequestMessage, 'default', array('class' => 'error'));
			$this->redirect(array('plugin' => 'core', 'controller' => 'menus', 'action' => 'index'));
			return;
		}
		if (!$this->request->is('post') && empty($this->request->data)) {
			$this->request->data = $this->Menu->MenuItem->findById($id);
		} else {
			if ($this->request->data['MenuItem']['parent_id'] === '') {
				$this->request->data['MenuItem']['parent_id'] = 0;
			}
			$this->Menu->MenuItem->Behaviors->load('Core.EnhancedTree', array(
				'scope' => array(
					'MenuItem.menu_id' => $this->request->data['MenuItem']['menu_id']
				)
			));
			if ($this->Menu->MenuItem->save($this->request->data)) {
				$this->Session->setFlash(__d('core', 'Menu Item <strong>%s</strong> has been updated.', array($this->request->data['MenuItem']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'edit', $this->request->data['MenuItem']['menu_id']));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$this->set(array(
			'menu' => $this->Menu->findById($this->request->data['MenuItem']['menu_id']),
			'menus' => $this->Menu->find('list', array('order' => 'Menu.name ASC')),
			'parentItems' => $this->Menu->MenuItem->generateTreeList(array(
				'MenuItem.menu_id' => $this->request->data['MenuItem']['menu_id'],
				'MenuItem.id <>' => $id,
				'or' => array(
					'MenuItem.lft <' => $this->request->data['MenuItem']['lft'],
					'MenuItem.lft >' => $this->request->data['MenuItem']['rght']
				)
			), null, null, '_', null, 2),
			'types' => $this->_getAvailableLinks()
		));
		$this->render('add_item');
	}

	/**
	 * Reorder items action
	 * AJAX POST
	 */
	public function reorder_items() {
		if ($this->request->is('ajax') && $this->request->is('post')) {
			if (empty($this->data) || !isset($this->data['MenuItem']) || empty($this->data['MenuItem'])) {
				$this->set('success', true);
			} else {
				$this->Menu->MenuItem->Behaviors->unload('Core.EnhancedTree');
				if ($this->Menu->MenuItem->saveAll($this->data['MenuItem'])) {
					$this->set('success', true);
				} else {
					$this->set('succes', false);
				}
			}
			$this->set('_serialize', array('success'));
		} else {
			throw new CakeException($this->invalidRequestMessage, 400);
		}
	}

	/**
	 * Get available Links via an Event trigger
	 * This fetches avilable Links from all activated Plugins.
	 *
	 * @return array
	 */
	protected function _getAvailableLinks() {
		$rawItems = WasabiEventManager::trigger($this, 'Backend.MenuItems.getAvailableLinks');
		$rawItems = $rawItems['Backend.MenuItems.getAvailableLinks'];

		$menuItems = array();
		foreach ($rawItems as $itemGroup) {
			foreach ($itemGroup as $name => $items) {
				$menuItems[$name] = $items;
			}
		}
		return $menuItems;
	}

}
