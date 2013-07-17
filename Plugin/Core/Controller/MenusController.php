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
	 * Edit action
	 * GET | POST
	 */
	public function add() {
		if ($this->request->is('post') && !empty($this->request->data)) {
			if (isset($this->request->data['MenuItem']['{UID}'])) {
				unset($this->request->data['MenuItem']['{UID}']);
			}
			foreach ($this->request->data['MenuItem'] as $key => $values) {
				if ($values['item'] === '' || !in_array($values['type'], array(MenuItem::TYPE_EXTERNAL_LINK, MenuItem::TYPE_OBJECT, MenuItem::TYPE_ACTION, MenuItem::TYPE_CUSTOM_ACTION))) {
					unset($this->request->data['MenuItem'][$key]);
				}
				if (isset($values['delete']) && $values['delete'] === '1') {
					$this->Menu->MenuItem->delete($values['id']);
					unset($this->request->data['MenuItem'][$key]);
				}
			}
			if ($this->Menu->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Session->setFlash(__d('core', 'The menu <strong>%s</strong> has been added successfully.', array($this->data['Menu']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$menuItems = $this->_getMenuItems();

		$this->set(array(
			'title_for_layout' => __d('core', 'Add a new Menu'),
			'menuItems' => $menuItems
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
			$this->request->data = $this->Menu->findWithMenuItemsById($id);
		} else {
			if (isset($this->request->data['MenuItem']['{UID}'])) {
				unset($this->request->data['MenuItem']['{UID}']);
			}
			foreach ($this->request->data['MenuItem'] as $key => $values) {
				if ($values['item'] === '' || !in_array($values['type'], array(MenuItem::TYPE_EXTERNAL_LINK, MenuItem::TYPE_OBJECT, MenuItem::TYPE_ACTION, MenuItem::TYPE_CUSTOM_ACTION))) {
					unset($this->request->data['MenuItem'][$key]);
				}
				if (isset($values['delete']) && $values['delete'] === '1') {
					$this->Menu->MenuItem->delete($values['id']);
					unset($this->request->data['MenuItem'][$key]);
				}
			}
			if ($this->Menu->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Session->setFlash(__d('core', 'The menu <strong>%s</strong> has been updated successfully.', array($this->data['Menu']['name'])), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
				return;
			} else {
				$this->Session->setFlash($this->formErrorMessage, 'default', array('class' => 'error'));
			}
		}

		$menuItems = $this->_getMenuItems();

		$this->set(array(
			'title_for_layout' => __d('core', 'Edit Menu'),
			'menuItems' => $menuItems
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

	protected function _getMenuItems() {
		$rawItems = WasabiEventManager::trigger($this, 'Backend.MenuItems.load');
		$rawItems = $rawItems['Backend.MenuItems.load'];

		$menuItems = array();
		foreach ($rawItems as $itemGroup) {
			foreach ($itemGroup as $name => $items) {
				$menuItems[$name] = $items;
			}
		}
		return $menuItems;
	}

}
